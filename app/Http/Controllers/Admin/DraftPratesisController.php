<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TesisDraft;
use Illuminate\Support\Facades\Storage;

class DraftPratesisController extends Controller
{
    public function index(Request $request)
    {
        // Start with base query including soft deleted records
        $query = TesisDraft::withTrashed()->with(['mahasiswa.user', 'dosenPembimbingSatu.user', 'dosenPembimbingDua.user']);
        $query = $this->applyFilters($query, $request);

        $drafts = $query->paginate(10)->through(function ($draft) {
            return [
                'id' => $draft->id,
                'us_judul' => $draft->us_judul,
                'us_abstrak' => $draft->us_abstrak,
                'status' => $draft->status,
                'tgl_pengajuan' => $draft->tgl_pengajuan,
                'file_sk_pembimbing' => $draft->file_sk_pembimbing,
                'file_khs' => $draft->file_khs,
                'file_krs' => $draft->file_krs,
                'ket_dospem_satu' => $draft->ket_dospem_satu,
                'ket_dospem_dua' => $draft->ket_dospem_dua,
                'mahasiswa' => [
                    'nim' => $draft->mahasiswa->nim ?? '',
                    'nama_mhs' => $draft->mahasiswa->nama_mhs ?? '',
                    'user' => [
                        'name' => $draft->mahasiswa->user->nama ?? '',
                    ],
                ],
                'dosen_pembimbing_satu' => [
                    'nama_dosen' => $draft->dosenPembimbingSatu->nama_dosen ?? '',
                    'user' => [
                        'name' => $draft->dosenPembimbingSatu->user->nama ?? '',
                    ],
                ],
                'dosen_pembimbing_dua' => [
                    'nama_dosen' => $draft->dosenPembimbingDua->nama_dosen ?? '',
                    'user' => [
                        'name' => $draft->dosenPembimbingDua->user->nama ?? '',
                    ],
                ],
                'status_badge' => $draft->status_badge,
                'created_at' => $draft->created_at->format('Y-m-d'),
            ];
        });

        return Inertia::render('admin/draft/Index', [
            'drafts' => $drafts,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    public function show($id)
    {
        $draft = TesisDraft::withTrashed()->with(['mahasiswa.user', 'dosenPembimbingSatu.user', 'dosenPembimbingDua.user'])
            ->findOrFail($id);

        return Inertia::render('admin/draft/Show', [
            'draft' => $draft
        ]);
    }

    public function update(Request $request, $id)
    {
        $draft = TesisDraft::withTrashed()->findOrFail($id);
        $action = $request->input('action');

        if ($action === 'approve') {
            $request->validate([
                'file_sk_pembimbing' => 'required|file|mimes:pdf|max:2048',
            ]);

            $fileSk = null;
            if ($request->hasFile('file_sk_pembimbing')) {
                $fileSk = $request->file('file_sk_pembimbing')->store('draft/sk_pembimbing', 'public');
            }

            $draft->update([
                'status' => 'approved',
                'file_sk_pembimbing' => $fileSk,
            ]);

            return redirect()->route('admin.draft.index')->with('message', [
                'type' => 'success',
                'title' => 'Draft Pratesis Berhasil Disetujui',
                'message' => 'Draft pratesis berhasil disetujui dan SK Pembimbing telah diupload.',
            ]);
        }

        if ($action === 'reject') {
            $request->validate([
                'alasan_penolakan' => 'required|string|max:500',
            ]);

            // Update status and store rejection reason, then soft delete
            $draft->update([
                'status' => 'rejected',
                'ket_dospem_satu' => $request->alasan_penolakan, // Store rejection reason
            ]);

            // Soft delete the record (keeps it in database but hides from normal queries)
            $draft->delete();

            return redirect()->route('admin.draft.index')->with('message', [
                'type' => 'success',
                'title' => 'Draft Pratesis Berhasil Ditolak',
                'message' => 'Draft pratesis telah ditolak dan dihapus dari sistem.',
            ]);
        }

        return redirect()->back()->with('error', 'Aksi tidak valid');
    }

    private function applyFilters($query, Request $request)
    {
        return $query
            ->when($request->filled('search'), fn($q) =>
                $q->where(fn($qq) =>
                    $qq->where('us_judul', 'like', "%{$request->search}%")
                       ->orWhereHas('mahasiswa.user', fn($qqq) =>
                           $qqq->where('nama', 'like', "%{$request->search}%")
                       )
                       ->orWhereHas('mahasiswa', fn($qqq) =>
                           $qqq->where('nim', 'like', "%{$request->search}%")
                       )
                )
            )
            ->when($request->filled('status'), function($q) use ($request) {
                // Filter by status and handle soft deletes appropriately
                return $q->where('status', $request->status);
            })
            ->when($request->filled('sort') && $request->filled('sortDirection'), function($q) use ($request) {
                $sortField = $request->sort;
                $sortDirection = $request->sortDirection;

                // Map frontend column names to database column names
                $columnMap = [
                    'nama' => 'mahasiswa.user.nama',
                    'nim' => 'mahasiswa.nim',
                    'us_judul' => 'us_judul',
                    'status' => 'status',
                    'created_at' => 'created_at',
                ];

                if (isset($columnMap[$sortField])) {
                    $dbColumn = $columnMap[$sortField];

                    if ($sortField === 'nama') {
                        return $q->leftJoin('mahasiswa', 'tesis_draft.mhs_id', '=', 'mahasiswa.id')
                                 ->leftJoin('users', 'mahasiswa.user_id', '=', 'users.id')
                                 ->orderBy('users.nama', $sortDirection);
                    }

                    if ($sortField === 'nim') {
                        return $q->leftJoin('mahasiswa', 'tesis_draft.mhs_id', '=', 'mahasiswa.id')
                                 ->orderBy('mahasiswa.nim', $sortDirection);
                    }

                    return $q->orderBy($dbColumn, $sortDirection);
                }

                return $q;
            }, fn($q) => $q->orderBy('created_at', 'desc'));
    }

}
