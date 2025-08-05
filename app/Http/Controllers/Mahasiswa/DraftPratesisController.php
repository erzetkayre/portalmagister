<?php

namespace App\Http\Controllers\Mahasiswa;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TesisDraft;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PDF;
class DraftPratesisController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }

        // Only get non-deleted drafts (soft delete excludes rejected drafts)
        $draft = TesisDraft::where('mhs_id', $mahasiswa->id)
            ->with(['mahasiswa.user', 'dosenPembimbingSatu.user', 'dosenPembimbingDua.user'])
            ->first();

        return Inertia::render('mahasiswa/draft/Index', [
            'draft' => $draft,
            'currentStep' => $this->getCurrentStep($draft)
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }

        // Check if active draft already exists (soft delete excludes rejected ones)
        $existingDraft = TesisDraft::where('mhs_id', $mahasiswa->id)->first();
        if ($existingDraft) {
            return redirect()->route('mahasiswa.draft.index');
        }

        // Get users with dosen role
        $dosens = Dosen::with('user')
            ->whereHas('user', function($query) {
                $query->whereHas('role', function($roleQuery) {
                    $roleQuery->where('nama_role', 'dosen');
                });
            })
            ->get();

        return Inertia::render('mahasiswa/draft/Create', [
            'dosens' => $dosens
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }

        // Check if draft already exists
        $existingDraft = TesisDraft::where('mhs_id', $mahasiswa->id)->where('status','!=','rejected')->first();
        if ($existingDraft) {
            return redirect()->route('mahasiswa.draft.index')->with('info', 'Anda sudah memiliki proposal yang sedang diproses');
        }

        $request->validate([
            'us_judul' => 'required|string|max:1000',
            'us_abstrak' => 'nullable|string',
            'us_dospem_satu' => 'required|exists:ref_dosen,id',
            'us_dospem_dua' => 'required|exists:ref_dosen,id|different:us_dospem_satu',
            'ket_dospem_satu' => 'required|string|max:500',
            'ket_dospem_dua' => 'required|string|max:500',
            'file_khs' => 'required|file|mimes:pdf|max:2048',
            'file_krs' => 'required|file|mimes:pdf|max:2048',
        ], [
            'us_judul.required' => 'Judul penelitian wajib diisi',
            'us_dospem_satu.required' => 'Dosen pembimbing 1 wajib dipilih',
            'us_dospem_dua.required' => 'Dosen pembimbing 2 wajib dipilih',
            'us_dospem_dua.different' => 'Dosen pembimbing 2 harus berbeda dengan dosen pembimbing 1',
            'ket_dospem_satu.required' => 'Alasan memilih dosen pembimbing 1 wajib diisi',
            'ket_dospem_dua.required' => 'Alasan memilih dosen pembimbing 2 wajib diisi',
            'file_khs.required' => 'File KHS wajib diunggah',
            'file_krs.required' => 'File KRS wajib diunggah',
        ]);

        // Generate custom filenames using NIM
        $nim = $mahasiswa->nim;
        $fileKhsName = $nim . '_file_khs.pdf';
        $fileKrsName = $nim . '_file_krs.pdf';

        // Store files with custom names
        $fileKhs = $request->file('file_khs')->storeAs('file_lampiran_khs', $fileKhsName, 'public');
        $fileKrs = $request->file('file_krs')->storeAs('file_lampiran_krs', $fileKrsName, 'public');

        TesisDraft::create([
            'mhs_id' => $mahasiswa->id,
            'us_judul' => $request->us_judul,
            'us_abstrak' => $request->us_abstrak,
            'us_dospem_satu' => $request->us_dospem_satu,
            'us_dospem_dua' => $request->us_dospem_dua,
            'ket_dospem_satu' => $request->ket_dospem_satu,
            'ket_dospem_dua' => $request->ket_dospem_dua,
            'file_khs' => $fileKhs,
            'file_krs' => $fileKrs,
            'tgl_pengajuan' => now(),
            'status' => 'pending'
        ]);

        return redirect()->route('mahasiswa.draft.index')->with('success', 'Draft pratesis berhasil diajukan dan sedang menunggu persetujuan admin');
    }

    public function show($id)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $draft = TesisDraft::where('mhs_id', $mahasiswa->id)
            ->where('id', $id)
            ->with(['dosenPembimbingSatu', 'dosenPembimbingDua'])
            ->firstOrFail();

        return Inertia::render('mahasiswa/draft/Show', [
            'draft' => $draft,
            'currentStep' => $this->getCurrentStep($draft)
        ]);
    }

    /**
     * Generate template surat permohonan bimbingan
     */

    public function generateSuratPermohonan($id)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;
        $draft = TesisDraft::with(['mahasiswa.user', 'dosenPembimbingSatu', 'dosenPembimbingDua'])
        ->where('mhs_id', $mahasiswa->id)
        ->where('id', $id)
        ->firstOrFail();

        // Cek apakah sudah ada file surat permohonan yang diunggah
        if ($draft->file_surat_permohonan !== null) {
            // Load data yang dibutuhkan
            $data = [
                'draft' => $draft,
                'mahasiswa' => $draft->mahasiswa,
                'dosen_pembimbing_1' => $draft->dosenPembimbingSatu,
                'dosen_pembimbing_2' => $draft->dosenPembimbingDua,
                'tanggal' => Carbon::now()->translatedFormat('d F Y'),
            ];

            $config = [
                'format' => 'A4-P',
                'margin_left' => 30,
                'margin_right' => 25,
                'margin_top' => 35,
                'margin_header' => 5,
                'margin_footer' => 5,
            ];

            $monthList = [
                'Jan' => 'Januari',
                'Feb' => 'Februari',
                'Mar' => 'Maret',
                'Apr' => 'April',
                'May' => 'Mei',
                'Jun' => 'Juni',
                'Jul' => 'Juli',
                'Aug' => 'Agustus',
                'Sep' => 'September',
                'Oct' => 'Oktober',
                'Nov' => 'November',
                'Dec' => 'Desember',
            ];

            $pdf = PDF::loadView('surat.cetak_permohonan', compact('data', 'monthList'), [], $config);
            return $pdf->stream('Surat_Permohonan_Bimbingan.pdf');

        } else {
            // Update kolom `file_surat_permohonan` untuk simulasikan sebagai telah dibuat (optional)
            $draft->update([
                'file_surat_permohonan' => 'generated_' . time() . '.pdf',
                'tgl_upload_surat' => now(),
            ]);

            // Load ulang data untuk PDF
            $data = [
                'draft' => $draft,
                'mahasiswa' => $draft->mahasiswa,
                'dosen_pembimbing_1' => $draft->dosenPembimbingSatu,
                'dosen_pembimbing_2' => $draft->dosenPembimbingDua,
                'tanggal' => Carbon::now()->translatedFormat('d F Y'),
            ];

            $config = [
                'format' => 'A4-P',
                'margin_left' => 30,
                'margin_right' => 25,
                'margin_top' => 35,
                'margin_header' => 5,
                'margin_footer' => 5,
            ];

            $monthList = [
                'Jan' => 'Januari',
                'Feb' => 'Februari',
                'Mar' => 'Maret',
                'Apr' => 'April',
                'May' => 'Mei',
                'Jun' => 'Juni',
                'Jul' => 'Juli',
                'Aug' => 'Agustus',
                'Sep' => 'September',
                'Oct' => 'Oktober',
                'Nov' => 'November',
                'Dec' => 'Desember',
            ];

            $pdf = PDF::loadView('surat.cetak_permohonan', compact('data', 'monthList'), [], $config);
            return $pdf->stream('Surat_Permohonan_Bimbingan.pdf');
        }
    }

    /**
     * Upload surat permohonan yang sudah ditandatangani
     */
    public function uploadSuratPermohonan(Request $request, $id)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        $draft = TesisDraft::where('mhs_id', $mahasiswa->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($draft->status !== 'pending') {
            return redirect()->back()->with('error', 'Upload surat hanya dapat dilakukan pada status pending');
        }

        $request->validate([
            'file_surat_permohonan' => 'required|file|mimes:pdf|max:5120', // 5MB max
        ], [
            'file_surat_permohonan.required' => 'File surat permohonan wajib diunggah',
            'file_surat_permohonan.mimes' => 'File harus berformat PDF',
            'file_surat_permohonan.max' => 'Ukuran file maksimal 5MB',
        ]);

        // Generate filename with NIM
        $nim = $mahasiswa->nim;
        $filename = $nim . '_surat_permohonan_bimbingan.pdf';

        // Delete old file if exists
        if ($draft->file_surat_permohonan) {
            Storage::disk('public')->delete($draft->file_surat_permohonan);
        }

        // Store new file
        $filePath = $request->file('file_surat_permohonan')->storeAs('surat_permohonan', $filename, 'public');

        // Update draft with new status
        $draft->update([
            'file_surat_permohonan' => $filePath,
            'tgl_upload_surat' => now(),
            'status' => 'surat_uploaded'
        ]);

        return redirect()->route('mahasiswa.draft.index')->with('success', 'Surat permohonan berhasil diunggah. Menunggu review lebih lanjut.');
    }

    private function getCurrentStep($draft)
    {
        if (!$draft) {
            return 1; // Form step
        }

        if ($draft->status === 'pending') {
            return 2; // Upload surat step
        }

        if ($draft->status === 'surat_uploaded') {
            return 3; // Waiting final approval
        }

        if ($draft->status === 'approved') {
            return 4; // Approved
        }

        return 1; // Default to form
    }
}
