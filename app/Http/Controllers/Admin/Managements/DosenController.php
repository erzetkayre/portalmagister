<?php

namespace App\Http\Controllers\Admin\Managements;

use Inertia\Inertia;
use App\Models\Role;
use App\Models\User;
use App\Models\Dosen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class DosenController extends Controller
{
    public function index(Request $request) {
        $query = Dosen::with('user')->select('*');
        $query = $this->applyFilters($query, $request);

        $dosen = $query->paginate(10)->through(function ($dsn) {
            return [
                'id' => $dsn->id,
                'name' => $dsn->nama_dosen,
                'nip' => $dsn->nip,
                'kode_dosen' => $dsn->kode_dosen,
                'status' => $dsn->status_dosen === 'aktif' ? 'active' : 'inactive',
                'email' => $dsn->user->email ?? '-',
                'created_at' => $dsn->created_at->format('Y-m-d'),
            ];
        });

        return Inertia::render('admin/managements/dosen/Index', [
            'dosen' => $dosen,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    public function create() {
        return Inertia::render('admin/managements/dosen/Create');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nip' => 'required|string|max:255|unique:users,nomor_induk',
            'kode_dosen' => 'required|string|max:255',
        ]);

        $role = Role::where('nama_role', 'dosen')->first();

        $user = User::create([
            'nama' => $request->name,
            'email' => $request->email,
            'nomor_induk' => $request->nip,
            'password' => bcrypt($request->nip),
            'role_id' => $role->id,
            'first_login' => 0,
            'is_active' => true,
        ]);

        Dosen::create([
            'user_id' => $user->id,
            'nip' => $request->nip,
            'kode_dosen' => $request->kode_dosen,
            'nama_dosen' => $request->name,
            'status_dosen' => 'aktif',
        ]);

        return redirect()->route('admin.dosen.index')
            ->with('message', [
                'type' => 'success',
                'title' => 'Dosen Berhasil Dibuat',
                'message' => 'Dosen baru telah ditambahkan ke sistem.',
            ]);
    }

    public function show($id) {
        $dosen = Dosen::with('user')->findOrFail($id);

        return Inertia::render('admin/managements/dosen/Show', [
            'dosen' => [
                'id' => $dosen->id,
                'name' => $dosen->nama_dosen,
                'nip' => $dosen->nip,
                'kode_dosen' => $dosen->kode_dosen,
                'bidang_keahlian' => $dosen->bidang_keahlian ?? '-',
                'gender' => $dosen->gender ?? 'L',
                'email' => $dosen->user->email ?? '-',
                'created_at' => $dosen->created_at->format('Y-m-d'),
            ]
        ]);
    }

    public function edit($id) {
        $dosen = Dosen::with('user')->findOrFail($id);

        return Inertia::render('admin/managements/dosen/Edit', [
            'dosen' => [
                'id' => $dosen->id,
                'name' => $dosen->nama_dosen,
                'nip' => $dosen->nip,
                'kode_dosen' => $dosen->kode_dosen,
                'bidang_keahlian' => $dosen->bidang_keahlian ?? '',
                'gender' => $dosen->gender ?? 'L',
                'email' => $dosen->user->email ?? '',
                'user_id' => $dosen->user_id,
            ]
        ]);
    }

    public function update(Request $request, $id) {
        $dosen = Dosen::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $dosen->user_id,
            'nip' => 'required|string|max:255|unique:users,nomor_induk,' . $dosen->user_id,
            'kode_dosen' => 'required|string|max:255',
            'bidang_keahlian' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
        ]);

        $dosen->user->update([
            'nama' => $request->name,
            'email' => $request->email,
            'nomor_induk' => $request->nip,
        ]);

        $dosen->update([
            'nip' => $request->nip,
            'kode_dosen' => $request->kode_dosen,
            'nama_dosen' => $request->name,
            'bidang_keahlian' => $request->bidang_keahlian,
            'gender' => $request->gender,
        ]);

        return redirect()->route('admin.dosen.index')
            ->with('message', [
                'type' => 'success',
                'title' => 'Dosen Berhasil Diperbarui',
                'message' => 'Data dosen berhasil diperbarui.',
            ]);
    }

    public function destroy($id) {
        $dosen = Dosen::with('user')->findOrFail($id);
        
        if ($dosen->user && $dosen->user->id === auth()->id()) {
            return redirect()->route('admin.dosen.index')->withErrors(['error' => 'Tidak dapat menghapus akun yang sedang anda gunakan']);
        }

        if ($dosen->user) {
            $dosen->user->delete();
        }
        $dosen->delete();

        return redirect()->route('admin.dosen.index')
            ->with('message', [
                'type' => 'success',
                'title' => 'Dosen Berhasil Dihapus',
                'message' => 'Data dosen berhasil dihapus.',
            ]);
    }

    private function applyFilters($query, Request $request){
        return $query
            ->when($request->filled('search'), fn($q) =>
                $q->where(fn($qq) =>
                    $qq->where('nama_dosen', 'like', "%{$request->search}%")
                       ->orWhere('nip', 'like', "%{$request->search}%")
                       ->orWhere('kode_dosen', 'like', "%{$request->search}%")
                )
            )
            ->when($request->filled('status'), fn($q) =>
                $q->where('status_dosen', $request->status === 'active' ? 'aktif' : 'tidak_aktif')
            )
            ->when($request->filled('sort') && $request->filled('sortDirection'), function($q) use ($request) {
                $sortField = $request->sort;
                $sortDirection = $request->sortDirection;
                
                $columnMap = [
                    'name' => 'nama_dosen',
                    'nip' => 'nip',
                    'kode_dosen' => 'kode_dosen',
                    'status' => 'status_dosen',
                    'created_at' => 'created_at',
                ];
                
                if (isset($columnMap[$sortField])) {
                    return $q->orderBy($columnMap[$sortField], $sortDirection);
                }
                
                return $q;
            }, fn($q) => $q->orderBy('created_at', 'desc'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $data = Excel::toArray([], $file)[0];
            
            // Skip header row
            $rows = array_slice($data, 1);
            
            $role = Role::where('nama_role', 'dosen')->first();
            $imported = 0;
            $errors = [];
            
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // Account for header and 0-based index
                
                // Validate required fields
                if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3])) {
                    $errors[] = "Baris {$rowNumber}: Data tidak lengkap";
                    continue;
                }
                
                $name = trim($row[0]);
                $nip = trim($row[1]);
                $email = trim($row[2]);
                $kode_dosen = trim($row[3]);
                
                // Check if NIP already exists
                if (User::where('nomor_induk', $nip)->exists()) {
                    $errors[] = "Baris {$rowNumber}: NIP {$nip} sudah terdaftar";
                    continue;
                }
                
                // Check if email already exists
                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris {$rowNumber}: Email {$email} sudah terdaftar";
                    continue;
                }
                
                try {
                    $user = User::create([
                        'nama' => $name,
                        'email' => $email,
                        'nomor_induk' => $nip,
                        'password' => bcrypt($nip),
                        'role_id' => $role->id,
                        'first_login' => 0,
                        'is_active' => true,
                    ]);
                    
                    Dosen::create([
                        'user_id' => $user->id,
                        'nip' => $nip,
                        'kode_dosen' => $kode_dosen,
                        'nama_dosen' => $name,
                        'status_dosen' => 'aktif',
                    ]);
                    
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: Error - " . $e->getMessage();
                }
            }
            
            $message = "Berhasil mengimpor {$imported} dosen";
            if (!empty($errors)) {
                $message .= ". Terdapat " . count($errors) . " error.";
            }
            
            return redirect()->back()->with('message', [
                'type' => $imported > 0 ? 'success' : 'error',
                'title' => 'Import Dosen',
                'message' => $message,
                'errors' => $errors,
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('message', [
                'type' => 'error',
                'title' => 'Import Gagal',
                'message' => 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage(),
            ]);
        }
    }
}
