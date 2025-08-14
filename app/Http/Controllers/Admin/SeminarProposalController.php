<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sempro;
use App\Models\TesisDraft;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class SeminarProposalController extends Controller
{
    public function index(Request $request)
    {
        $query = Sempro::query()
            ->with([
                'tesis.mahasiswa.user',
                'tesis.dosenPembimbingSatu.user',
                'tesis.dosenPembimbingDua.user'
            ]);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tesis.mahasiswa', function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama_mhs', 'like', "%{$search}%");
            })
            ->orWhereHas('tesis.mahasiswa.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('tesis', function ($q) use ($search) {
                $q->where('us_judul', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status_seminar', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('sortDirection', 'desc');

        if ($sortBy === 'nama') {
            $query->join('tesis_draft', 'tesis_ujian_proposal.tesis_id', '=', 'tesis_draft.id')
                  ->join('mahasiswa', 'tesis_draft.mhs_id', '=', 'mahasiswa.id')
                  ->join('users', 'mahasiswa.user_id', '=', 'users.id')
                  ->orderBy('users.name', $sortDirection)
                  ->select('tesis_ujian_proposal.*');
        } elseif ($sortBy === 'nim') {
            $query->join('tesis_draft', 'tesis_ujian_proposal.tesis_id', '=', 'tesis_draft.id')
                  ->join('mahasiswa', 'tesis_draft.mhs_id', '=', 'mahasiswa.id')
                  ->orderBy('mahasiswa.nim', $sortDirection)
                  ->select('tesis_ujian_proposal.*');
        } elseif ($sortBy === 'us_judul') {
            $query->join('tesis_draft', 'tesis_ujian_proposal.tesis_id', '=', 'tesis_draft.id')
                  ->orderBy('tesis_draft.us_judul', $sortDirection)
                  ->select('tesis_ujian_proposal.*');
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        $sempros = $query->paginate(10)
            ->through(function ($sempro) {
                return [
                    'id' => $sempro->id,
                    'tesis_id' => $sempro->tesis_id,
                    'tanggal' => $sempro->tanggal?->format('Y-m-d'),
                    'tempat' => $sempro->tempat,
                    'jam_mulai' => $sempro->jam_mulai?->format('H:i'),
                    'jam_selesai' => $sempro->jam_selesai?->format('H:i'),
                    'status_seminar' => $sempro->status_seminar,
                    'kartu_bimbingan' => $sempro->kartu_bimbingan,
                    'draft_semhas' => $sempro->draft_semhas,
                    'surat_kelayakan' => $sempro->surat_kelayakan,
                    'tgl_upload_surat' => $sempro->tgl_upload_surat?->format('Y-m-d H:i'),
                    'summary' => $sempro->summary,
                    'catatan' => $sempro->catatan,
                    'berita_acara' => $sempro->berita_acara,
                    'tesis' => [
                        'us_judul' => $sempro->tesis->us_judul,
                        'us_abstrak' => $sempro->tesis->us_abstrak,
                        'mahasiswa' => [
                            'nim' => $sempro->tesis->mahasiswa->nim,
                            'nama_mhs' => $sempro->tesis->mahasiswa->nama_mhs,
                            'user' => [
                                'name' => $sempro->tesis->mahasiswa->user->name,
                            ]
                        ],
                        'dosen_pembimbing_satu' => [
                            'nama_dosen' => $sempro->tesis->dosenPembimbingSatu->nama_dosen,
                            'user' => [
                                'name' => $sempro->tesis->dosenPembimbingSatu->user->name,
                                'email' => $sempro->tesis->dosenPembimbingSatu->user->email,
                            ]
                        ],
                        'dosen_pembimbing_dua' => [
                            'nama_dosen' => $sempro->tesis->dosenPembimbingDua->nama_dosen,
                            'user' => [
                                'name' => $sempro->tesis->dosenPembimbingDua->user->name,
                                'email' => $sempro->tesis->dosenPembimbingDua->user->email,
                            ]
                        ]
                    ],
                    'status_badge' => $sempro->status_badge,
                    'formatted_date' => $sempro->formatted_date,
                    'formatted_time' => $sempro->formatted_time,
                ];
            });

        return Inertia::render('admin/sempro/Index', [
            'sempros' => $sempros,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ]
        ]);
    }

    public function show($id)
    {
        $sempro = Sempro::with([
            'tesis.mahasiswa.user',
            'tesis.dosenPembimbingSatu.user',
            'tesis.dosenPembimbingDua.user'
        ])->findOrFail($id);

        return Inertia::render('admin/sempro/Show', [
            'sempro' => [
                'id' => $sempro->id,
                'tesis_id' => $sempro->tesis_id,
                'tanggal' => $sempro->tanggal?->format('Y-m-d'),
                'tempat' => $sempro->tempat,
                'jam_mulai' => $sempro->jam_mulai?->format('H:i'),
                'jam_selesai' => $sempro->jam_selesai?->format('H:i'),
                'status_seminar' => $sempro->status_seminar,
                'kartu_bimbingan' => $sempro->kartu_bimbingan,
                'draft_semhas' => $sempro->draft_semhas,
                'surat_kelayakan' => $sempro->surat_kelayakan,
                'tgl_upload_surat' => $sempro->tgl_upload_surat?->format('Y-m-d H:i'),
                'summary' => $sempro->summary,
                'catatan' => $sempro->catatan,
                'berita_acara' => $sempro->berita_acara,
                'tesis' => [
                    'us_judul' => $sempro->tesis->us_judul,
                    'us_abstrak' => $sempro->tesis->us_abstrak,
                    'mahasiswa' => [
                        'nim' => $sempro->tesis->mahasiswa->nim,
                        'nama_mhs' => $sempro->tesis->mahasiswa->nama_mhs,
                        'user' => [
                            'name' => $sempro->tesis->mahasiswa->user->name,
                        ]
                    ],
                    'dosen_pembimbing_satu' => [
                        'nama_dosen' => $sempro->tesis->dosenPembimbingSatu->nama_dosen,
                        'user' => [
                            'name' => $sempro->tesis->dosenPembimbingSatu->user->name,
                            'email' => $sempro->tesis->dosenPembimbingSatu->user->email,
                        ]
                    ],
                    'dosen_pembimbing_dua' => [
                        'nama_dosen' => $sempro->tesis->dosenPembimbingDua->nama_dosen,
                        'user' => [
                            'name' => $sempro->tesis->dosenPembimbingDua->user->name,
                            'email' => $sempro->tesis->dosenPembimbingDua->user->email,
                        ]
                    ]
                ],
                'status_badge' => $sempro->status_badge,
                'formatted_date' => $sempro->formatted_date,
                'formatted_time' => $sempro->formatted_time,
            ]
        ]);
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $sempro = Sempro::findOrFail($id);

            // Update status to approved
            $sempro->update([
                'status_seminar' => 'approved',
                'catatan' => $request->catatan,
            ]);

            DB::commit();

            // Send notification to student
            $this->sendApprovalNotification($sempro, 'approved');

            return redirect()->back()->with('flash', [
                'message' => [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'message' => 'Pengajuan seminar proposal berhasil disetujui.'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('flash', [
                'message' => [
                    'type' => 'error',
                    'title' => 'Gagal!',
                    'message' => 'Terjadi kesalahan saat memproses persetujuan: ' . $e->getMessage()
                ]
            ]);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $sempro = Sempro::findOrFail($id);

            // Update status to rejected
            $sempro->update([
                'status_seminar' => 'rejected',
                'catatan' => $request->catatan,
            ]);

            DB::commit();

            // Send notification to student
            $this->sendApprovalNotification($sempro, 'rejected');

            return redirect()->back()->with('flash', [
                'message' => [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'message' => 'Pengajuan seminar proposal berhasil ditolak.'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('flash', [
                'message' => [
                    'type' => 'error',
                    'title' => 'Gagal!',
                    'message' => 'Terjadi kesalahan saat menolak pengajuan: ' . $e->getMessage()
                ]
            ]);
        }
    }

    public function approveSchedule(Request $request, $id)
    {
        $request->validate([
            'blast_email' => 'boolean',
            'catatan_jadwal' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $sempro = Sempro::with([
                'tesis.mahasiswa.user',
                'tesis.dosenPembimbingSatu.user',
                'tesis.dosenPembimbingDua.user'
            ])->findOrFail($id);

            // Validate that schedule is set
            if (!$sempro->hasSchedule()) {
                throw new \Exception('Jadwal belum diatur oleh mahasiswa');
            }

            // Update status to scheduled
            $sempro->update([
                'status_seminar' => 'scheduled',
                'catatan' => $request->catatan_jadwal,
            ]);

            // Generate invitation PDF
            $invitationPath = $this->generateInvitation($sempro);

            // Send email notifications if requested
            if ($request->blast_email) {
                $this->sendScheduleNotifications($sempro, $invitationPath);
            }

            DB::commit();

            return redirect()->back()->with('flash', [
                'message' => [
                    'type' => 'success',
                    'title' => 'Berhasil!',
                    'message' => 'Jadwal seminar proposal berhasil disetujui dan undangan telah dibuat.'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('flash', [
                'message' => [
                    'type' => 'error',
                    'title' => 'Gagal!',
                    'message' => 'Terjadi kesalahan saat menyetujui jadwal: ' . $e->getMessage()
                ]
            ]);
        }
    }

    private function generateInvitation(Sempro $sempro)
    {
        $data = [
            'sempro' => $sempro,
            'mahasiswa' => $sempro->tesis->mahasiswa,
            'dosen_pembimbing_satu' => $sempro->tesis->dosenPembimbingSatu,
            'dosen_pembimbing_dua' => $sempro->tesis->dosenPembimbingDua,
            'tanggal' => $sempro->formatted_date,
            'waktu' => $sempro->formatted_time,
            'tempat' => $sempro->tempat,
            'judul' => $sempro->tesis->us_judul,
        ];

        $pdf = PDF::loadView('pdf.invitation-sempro', $data);

        $filename = "undangan_sempro_{$sempro->tesis->mahasiswa->nim}_{$sempro->id}.pdf";
        $path = "invitations/sempro/{$filename}";

        Storage::put($path, $pdf->output());

        return $path;
    }

    private function sendScheduleNotifications(Sempro $sempro, $invitationPath)
    {
        $emails = [
            $sempro->tesis->mahasiswa->user->email,
            $sempro->tesis->dosenPembimbingSatu->user->email,
            $sempro->tesis->dosenPembimbingDua->user->email,
        ];

        $data = [
            'mahasiswa_nama' => $sempro->tesis->mahasiswa->user->name,
            'mahasiswa_nim' => $sempro->tesis->mahasiswa->nim,
            'judul' => $sempro->tesis->us_judul,
            'tanggal' => $sempro->formatted_date,
            'waktu' => $sempro->formatted_time,
            'tempat' => $sempro->tempat,
            'invitation_path' => $invitationPath,
        ];

        foreach ($emails as $email) {
            try {
                Mail::send('emails.sempro-schedule', $data, function ($message) use ($email, $data, $invitationPath) {
                    $message->to($email)
                        ->subject('Undangan Seminar Proposal - ' . $data['mahasiswa_nama']);

                    if (Storage::exists($invitationPath)) {
                        $message->attach(Storage::path($invitationPath), [
                            'as' => 'Undangan_Seminar_Proposal.pdf',
                            'mime' => 'application/pdf'
                        ]);
                    }
                });
            } catch (\Exception $e) {
                \Log::error("Failed to send sempro notification to {$email}: " . $e->getMessage());
            }
        }
    }

    private function sendApprovalNotification(Sempro $sempro, $status)
    {
        $data = [
            'mahasiswa_nama' => $sempro->tesis->mahasiswa->user->name,
            'mahasiswa_nim' => $sempro->tesis->mahasiswa->nim,
            'judul' => $sempro->tesis->us_judul,
            'status' => $status,
            'catatan' => $sempro->catatan,
        ];

        try {
            Mail::send('emails.sempro-approval', $data, function ($message) use ($sempro, $status, $data) {
                $message->to($sempro->tesis->mahasiswa->user->email)
                    ->subject('Status Pengajuan Seminar Proposal - ' . $data['mahasiswa_nama']);
            });
        } catch (\Exception $e) {
            \Log::error("Failed to send sempro approval notification: " . $e->getMessage());
        }
    }

    public function downloadInvitation($id)
    {
        $sempro = Sempro::with([
            'tesis.mahasiswa.user',
            'tesis.dosenPembimbingSatu.user',
            'tesis.dosenPembimbingDua.user'
        ])->findOrFail($id);

        if ($sempro->status_seminar !== 'scheduled') {
            return redirect()->back()->with('flash', [
                'message' => [
                    'type' => 'error',
                    'title' => 'Gagal!',
                    'message' => 'Undangan hanya dapat diunduh untuk seminar yang sudah dijadwalkan.'
                ]
            ]);
        }

        $invitationPath = $this->generateInvitation($sempro);

        return Storage::download($invitationPath, "Undangan_Sempro_{$sempro->tesis->mahasiswa->nim}.pdf");
    }
}
