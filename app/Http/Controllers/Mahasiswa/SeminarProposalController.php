<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

use App\Models\Mahasiswa;
use App\Models\Tesis;
use App\Models\TesisSempro;
use App\Models\Ruang;

class SeminarProposalController extends Controller
{
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $tesis = Tesis::where('mhs_id', $mahasiswa->id)->first();
        $sempro = $tesis ? TesisSempro::where('tesis_id', $tesis->id)->with('ruang')->first() : null;

        return Inertia::render('mahasiswa/sempro/Index', [
            'sempro' => $sempro
        ]);
    }

    public function create()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $tesis = Tesis::where('mhs_id', $mahasiswa->id)->first();
        if (!$tesis) {
            return redirect()->back()->with('error', 'Data tesis tidak ditemukan.');
        }

        $existing = TesisSempro::where('tesis_id', $tesis->id)->whereNull('deleted_at')->first();
        if ($existing) {
            return redirect()->route('mahasiswa.sempro.index')->with('info', 'Anda sudah mengajukan seminar proposal.');
        }

        $ruangs = Ruang::all();

        return Inertia::render('mahasiswa/sempro/Create', [
            'ruangs' => $ruangs
        ]);
    }

    public function store(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $tesis = Tesis::where('mhs_id', $mahasiswa->id)->first();

        if (!$tesis) {
            return redirect()->back()->with('error', 'Data tesis tidak ditemukan.');
        }

        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'tempat' => 'required|exists:ref_ruang,id',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'draft_sempro' => 'required|file|mimes:pdf|max:3072',
            'summary' => 'nullable|string',
        ], [
            'draft_sempro.required' => 'File draft seminar proposal wajib diunggah.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        // Simpan file draft
        $file = $request->file('draft_sempro');
        $fileName = $mahasiswa->nim . '_draft_sempro.pdf';
        $path = $file->storeAs('draft_sempro', $fileName, 'public');

        TesisSempro::create([
            'tesis_id' => $tesis->id,
            'tanggal' => $request->tanggal,
            'tempat' => $request->tempat,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'draft_sempro' => $path,
            'summary' => $request->summary,
            'status_seminar' => 'waiting'
        ]);

        return redirect()->route('mahasiswa.sempro.index')->with('success', 'Pendaftaran seminar proposal berhasil dikirim.');
    }

    public function show($id)
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $tesis = Tesis::where('mhs_id', $mahasiswa->id)->firstOrFail();

        $sempro = TesisSempro::where('id', $id)
            ->where('tesis_id', $tesis->id)
            ->with('ruang')
            ->firstOrFail();

        return Inertia::render('mahasiswa/sempro/Show', [
            'sempro' => $sempro
        ]);
    }
}
