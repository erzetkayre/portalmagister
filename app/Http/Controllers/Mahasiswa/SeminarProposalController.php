<?php

namespace App\Http\Controllers\Mahasiswa;

use Inertia\Inertia;
use App\Models\Ruang;
use App\Models\Tesis;
use App\Models\Sempro;
use App\Models\Mahasiswa;

use App\Models\TesisDraft;
use App\Models\TesisSempro;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Tambahkan ini
use Illuminate\Support\Facades\Storage;

class SeminarProposalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }

        // Get tesis that is approved (from previous draft process)
        $tesis = TesisDraft::where('mhs_id', $mahasiswa->id)
            ->where('status', 'approved')
            ->with(['mahasiswa.user', 'dosenPembimbingSatu.user', 'dosenPembimbingDua.user'])
            ->first();

        if (!$tesis) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Belum ada tesis yang disetujui');
        }

        // Get ujian proposal for this tesis
        $ujianProposal = Sempro::where('tesis_id', $tesis->id)
            ->first();

        return Inertia::render('mahasiswa/sempro/Index', [
            'tesis' => $tesis,
            'ujianProposal' => $ujianProposal,
            'currentStep' => $this->getCurrentStep($ujianProposal)
        ]);
    }

    /**
     * Store kartu bimbingan and create initial ujian proposal record
     */
    public function store(Request $request)
{
    $user = Auth::user();
    $mahasiswa = $user->mahasiswa;

    if (!$mahasiswa) {
        return redirect()->route('mahasiswa.dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
    }

    // Get approved tesis
    $tesis = TesisDraft::where('mhs_id', $mahasiswa->id)
        ->where('status', 'approved')
        ->first();

    if (!$tesis) {
        return redirect()->route('mahasiswa.dashboard')->with('error', 'Belum ada tesis yang disetujui');
    }

    // Check if ujian proposal already exists
    $existingUjian = Sempro::where('tesis_id', $tesis->id)->first();
    if ($existingUjian) {
        return redirect()->route('mahasiswa.sempro.index')->with('info', 'Ujian proposal sudah diajukan');
    }

    $request->validate([
        'kartu_bimbingan' => 'required|file|mimes:pdf|max:10240', // 10MB max
    ], [
        'kartu_bimbingan.required' => 'Kartu bimbingan wajib diunggah',
        'kartu_bimbingan.mimes' => 'File harus berformat PDF',
        'kartu_bimbingan.max' => 'Ukuran file maksimal 10MB',
    ]);

    try {
        // Generate custom filename using NIM
        $nim = $mahasiswa->nim;
        $kartuFilename = $nim . '_kartu_bimbingan.pdf';

        // Store file with custom name
        $kartuPath = $request->file('kartu_bimbingan')->storeAs('kartu_bimbingan', $kartuFilename, 'public');

        // Debug: Log untuk memastikan file tersimpan
        \Log::info('File stored at: ' . $kartuPath);
        \Log::info('Tesis ID: ' . $tesis->id);

        // Create ujian proposal record with initial data
        $sempro = Sempro::create([
            'tesis_id' => $tesis->id,
            'kartu_bimbingan' => $kartuPath,
            'status_seminar' => 'kartu_uploaded',
            'tanggal' => null,
            'tempat' => null,
            'jam_mulai' => null,
            'jam_selesai' => null,
            'draft_semhas' => null,
            'surat_kelayakan' => null,
            'summary' => null,
            'catatan' => null,
            'berita_acara' => null,
        ]);

        // Debug: Log untuk memastikan data tersimpan
        \Log::info('Sempro created with ID: ' . $sempro->id);

        return redirect()->route('mahasiswa.sempro.index')->with('success', 'Kartu bimbingan berhasil diupload. Silakan lanjut ke tahap berikutnya.');

    } catch (\Exception $e) {
        // Log error untuk debugging
        \Log::error('Error storing kartu bimbingan: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'Gagal menyimpan kartu bimbingan: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Generate template surat kelayakan ujian proposal
     */
    public function generateSuratKelayakan($id)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $ujianProposal = Sempro::with(['tesis.mahasiswa.user', 'tesis.dosenPembimbingSatu.user', 'tesis.dosenPembimbingDua.user'])
            ->whereHas('tesis', function($query) use ($mahasiswa) {
                $query->where('mhs_id', $mahasiswa->id);
            })
            ->where('id', $id)
            ->firstOrFail();

        // Generate PDF template
        $pdf = PDF::loadView('surat.template_kelayakan_ujian_proposal', compact('ujianProposal'));
        return $pdf->stream('Template_Surat_Kelayakan_Ujian_Proposal.pdf');
    }

    /**
     * Upload surat kelayakan yang sudah ditandatangani
     */
    public function uploadSuratKelayakan(Request $request, $id)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $ujianProposal = Sempro::whereHas('tesis', function($query) use ($mahasiswa) {
            $query->where('mhs_id', $mahasiswa->id);
        })->where('id', $id)->firstOrFail();

        if ($ujianProposal->status_seminar !== 'kartu_uploaded') {
            return redirect()->back()->with('error', 'Upload surat hanya dapat dilakukan setelah kartu bimbingan diupload');
        }

        $request->validate([
            'file_surat_kelayakan' => 'required|file|mimes:pdf|max:5120', // 5MB max
        ], [
            'file_surat_kelayakan.required' => 'File surat kelayakan wajib diunggah',
            'file_surat_kelayakan.mimes' => 'File harus berformat PDF',
            'file_surat_kelayakan.max' => 'Ukuran file maksimal 5MB',
        ]);

        // Generate filename with NIM
        $nim = $mahasiswa->nim;
        $filename = $nim . '_surat_kelayakan_ujian.pdf';

        // Delete old file if exists
        if ($ujianProposal->surat_kelayakan) {
            Storage::disk('public')->delete($ujianProposal->surat_kelayakan);
        }

        // Store new file
        $filePath = $request->file('file_surat_kelayakan')->storeAs('surat_kelayakan', $filename, 'public');

        // Update ujian proposal with new status
        $ujianProposal->update([
            'surat_kelayakan' => $filePath,
            'status_seminar' => 'waiting' // Move to waiting for admin approval
        ]);

        return redirect()->route('mahasiswa.sempro.index')->with('success', 'Surat kelayakan berhasil diunggah. Menunggu persetujuan admin.');
    }

    /**
     * Update jadwal ujian proposal oleh mahasiswa (Step 4)
     */
    public function updateJadwal(Request $request, $id)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $ujianProposal = Sempro::whereHas('tesis', function($query) use ($mahasiswa) {
            $query->where('mhs_id', $mahasiswa->id);
        })->where('id', $id)->firstOrFail();

        // Only allow if admin has approved
        if ($ujianProposal->status_seminar !== 'approved') {
            return redirect()->back()->with('error', 'Anda belum diizinkan mengisi jadwal ujian');
        }

        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'tempat' => 'required|string|max:255',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ], [
            'tanggal.required' => 'Tanggal ujian wajib diisi',
            'tanggal.after_or_equal' => 'Tanggal ujian tidak boleh kurang dari hari ini',
            'tempat.required' => 'Tempat ujian wajib diisi',
            'jam_mulai.required' => 'Jam mulai wajib diisi',
            'jam_selesai.required' => 'Jam selesai wajib diisi',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai',
        ]);

        $ujianProposal->update([
            'tanggal' => $request->tanggal,
            'tempat' => $request->tempat,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'status_seminar' => 'scheduled' // Mark as scheduled
        ]);

        return redirect()->route('mahasiswa.sempro.index')->with('success', 'Jadwal ujian proposal berhasil diatur.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $ujianProposal = Sempro::with(['tesis.mahasiswa.user', 'tesis.dosenPembimbingSatu.user', 'tesis.dosenPembimbingDua.user'])
            ->whereHas('tesis', function($query) use ($mahasiswa) {
                $query->where('mhs_id', $mahasiswa->id);
            })
            ->where('id', $id)
            ->firstOrFail();

        return Inertia::render('mahasiswa/sempro/Show', [
            'ujianProposal' => $ujianProposal,
            'currentStep' => $this->getCurrentStep($ujianProposal)
        ]);
    }

    private function getCurrentStep($ujianProposal)
    {
        if (!$ujianProposal) {
            return 1; // Upload kartu bimbingan step
        }

        switch ($ujianProposal->status_seminar) {
            case 'kartu_uploaded':
                return 2; // Upload surat kelayakan step
            case 'waiting':
                return 3; // Waiting admin approval
            case 'approved':
                return 4; // Fill date and place
            case 'scheduled':
            case 'done':
                return 5; // Completed
            default:
                return 1; // Default to first step
        }
    }
}
