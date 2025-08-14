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
use Illuminate\Support\Facades\Log;
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
            'currentStep' => $this->getCurrentStep($ujianProposal),
            'canProceedToNext' => $this->canProceedToNextStep($ujianProposal)
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

            return redirect()->route('mahasiswa.sempro.index')->with('success', 'Kartu bimbingan berhasil diupload. Silakan lanjut ke tahap berikutnya.');

        } catch (\Exception $e) {
            \Log::error('Error storing kartu bimbingan: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal menyimpan kartu bimbingan: ' . $e->getMessage())
                ->withInput();
        }
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
            'status_seminar' => 'waiting', // Move to waiting for admin approval
            'tgl_upload_surat' => now()
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
            'status_seminar' => 'scheduled' // Langsung dijadwalkan
        ]);

        return redirect()->route('mahasiswa.sempro.index')->with('success', 'Jadwal ujian proposal berhasil diatur!');
    }

    /**
     * Upload draft semhas (Step 5)
     */
    public function uploadDraftSemhas(Request $request, $id)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $ujianProposal = Sempro::whereHas('tesis', function($query) use ($mahasiswa) {
            $query->where('mhs_id', $mahasiswa->id);
        })->where('id', $id)->firstOrFail();

        // Only allow if schedule is approved
        if ($ujianProposal->status_seminar !== 'scheduled') {
            return redirect()->back()->with('error', 'Upload draft semhas hanya dapat dilakukan setelah jadwal disetujui');
        }

        $request->validate([
            'file_draft_semhas' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ], [
            'file_draft_semhas.required' => 'File draft semhas wajib diunggah',
            'file_draft_semhas.mimes' => 'File harus berformat PDF',
            'file_draft_semhas.max' => 'Ukuran file maksimal 10MB',
        ]);

        // Generate filename with NIM
        $nim = $mahasiswa->nim;
        $filename = $nim . '_draft_semhas.pdf';

        // Delete old file if exists
        if ($ujianProposal->draft_semhas) {
            Storage::disk('public')->delete($ujianProposal->draft_semhas);
        }

        // Store new file
        $filePath = $request->file('file_draft_semhas')->storeAs('draft_semhas', $filename, 'public');

        // Update ujian proposal with draft semhas
        $ujianProposal->update([
            'draft_semhas' => $filePath,
            'status_seminar' => 'ready_for_exam' // Ready for examination
        ]);

        return redirect()->route('mahasiswa.sempro.index')->with('success', 'Draft semhas berhasil diunggah. Seminar proposal siap dilaksanakan.');
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
            'currentStep' => $this->getCurrentStep($ujianProposal),
            'canProceedToNext' => $this->canProceedToNextStep($ujianProposal)
        ]);
    }

    /**
     * Get current step based on status
     */
    private function getCurrentStep($ujianProposal)
    {
        if (!$ujianProposal) {
            return 1; // Upload kartu bimbingan step
        }

        switch ($ujianProposal->status_seminar) {
            case 'kartu_uploaded':
                return 2; // Upload surat kelayakan step
            case 'waiting':
                return 3; // Waiting admin approval for surat
            case 'approved':
                return 4; // Fill schedule (date, time, place)
            case 'schedule_pending':
                return 4; // Schedule submitted, waiting admin approval
            case 'scheduled':
                return 5; // Upload draft semhas
            case 'ready_for_exam':
            case 'done':
                return 6; // Completed/Ready for exam
            default:
                return 1; // Default to first step
        }
    }

    /**
     * Check if can proceed to next step
     */
    private function canProceedToNextStep($ujianProposal)
    {
        if (!$ujianProposal) {
            return true; // Can start step 1
        }

        switch ($ujianProposal->status_seminar) {
            case 'kartu_uploaded':
                return true; // Can proceed to step 2 (upload surat)
            case 'waiting':
                return false; // Must wait for admin approval
            case 'approved':
                return true; // Can proceed to step 4 (fill schedule)
            case 'schedule_pending':
                return false; // Must wait for admin to approve schedule
            case 'scheduled':
                return true; // Can proceed to step 5 (upload draft semhas)
            case 'ready_for_exam':
                return false; // Process completed
            case 'done':
                return false; // Process completed
            default:
                return false;
        }
    }
}
