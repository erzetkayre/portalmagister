<?php

namespace App\Http\Controllers\Admin\Managements;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    public function index(Request $request) {
        $query = Mahasiswa::with('user')->select('*');
        $query = $this->applyFilters($query, $request);

        $mahasiswa = $query->paginate(10)->through(function ($mhs) {
            return [
                'id' => $mhs->id,
                'name' => $mhs->nama_mhs,
                'nim' => $mhs->nim,
                'angkatan' => $mhs->angkatan,
                'status' => $mhs->status_mhs === 'aktif' ? 'active' : 'inactive',
                'gender' => $mhs->gender,
                'email' => $mhs->user->email ?? '-',
                'created_at' => $mhs->created_at->format('Y-m-d'),
            ];
        });

        $angkatan = Mahasiswa::select('angkatan')->distinct()->orderBy('angkatan', 'desc')->get()->pluck('angkatan');
        $gender = Mahasiswa::select('gender')->distinct()->get()->pluck('gender');

        return Inertia::render('admin/managements/mahasiswa/Index', [
            'mahasiswa' => $mahasiswa,
            'angkatan' => $angkatan,
            'gender' => $gender,
            'filters' => [
                'search' => $request->search,
                'angkatan' => $request->angkatan,
                'status' => $request->status,
                'gender' => $request->gender,
            ],
        ]);
    }

    public function create() {
        return Inertia::render('admin/managements/mahasiswa/Create');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nim' => 'required|string|max:255|unique:users,nomor_induk',
            'angkatan' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'gender' => 'required|in:L,P',
        ]);

        $role = Role::where('nama_role', 'mahasiswa')->first();

        $user = User::create([
            'nama' => $request->name,
            'email' => $request->email,
            'nomor_induk' => $request->nim,
            'password' => bcrypt($request->nim),
            'role_id' => $role->id,
            'first_login' => 0,
            'is_active' => true,
        ]);

        Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $request->nim,
            'nama_mhs' => $request->name,
            'angkatan' => $request->angkatan,
            'gender' => $request->gender,
            'status_mhs' => 'aktif',
        ]);

        return redirect()->route('admin.mahasiswa.index')
            ->with('message', [
                'type' => 'success',
                'title' => 'Mahasiswa Berhasil Dibuat',
                'message' => 'Mahasiswa baru telah ditambahkan ke sistem.',
            ]);
    }

    public function show($id) {
        $mahasiswa = Mahasiswa::with('user')->findOrFail($id);

        return Inertia::render('admin/managements/mahasiswa/Show', [
            'mahasiswa' => [
                'id' => $mahasiswa->id,
                'name' => $mahasiswa->nama_mhs,
                'nim' => $mahasiswa->nim,
                'angkatan' => $mahasiswa->angkatan,
                'gender' => $mahasiswa->gender,
                'status' => $mahasiswa->status_mhs,
                'email' => $mahasiswa->user->email ?? '-',
                'created_at' => $mahasiswa->created_at->format('Y-m-d'),
            ]
        ]);
    }

    public function edit($id) {
        $mahasiswa = Mahasiswa::with('user')->findOrFail($id);

        return Inertia::render('admin/managements/mahasiswa/Edit', [
            'mahasiswa' => [
                'id' => $mahasiswa->id,
                'name' => $mahasiswa->nama_mhs,
                'nim' => $mahasiswa->nim,
                'angkatan' => $mahasiswa->angkatan,
                'gender' => $mahasiswa->gender,
                'email' => $mahasiswa->user->email ?? '',
                'user_id' => $mahasiswa->user_id,
            ],
            'angkatan' => Mahasiswa::select('angkatan')->distinct()->orderBy('angkatan', 'desc')->get()->pluck('angkatan'),
        ]);
    }

    public function update(Request $request, $id) {
        $mahasiswa = Mahasiswa::with('user')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $mahasiswa->user_id,
            'nim' => 'required|string|max:255|unique:users,nomor_induk,' . $mahasiswa->user_id,
            'angkatan' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'gender' => 'required|in:L,P',
        ]);

        $mahasiswa->user->update([
            'nama' => $request->name,
            'email' => $request->email,
            'nomor_induk' => $request->nim,
        ]);

        $mahasiswa->update([
            'nim' => $request->nim,
            'nama_mhs' => $request->name,
            'angkatan' => $request->angkatan,
            'gender' => $request->gender,
        ]);

        return redirect()->route('admin.mahasiswa.index')
            ->with('message', [
                'type' => 'success',
                'title' => 'Mahasiswa Berhasil Diperbarui',
                'message' => 'Data mahasiswa berhasil diperbarui.',
            ]);
    }

    public function destroy($id) {
        $mahasiswa = Mahasiswa::with('user')->findOrFail($id);

        if ($mahasiswa->user && $mahasiswa->user->id === auth()->id()) {
            return redirect()->route('admin.mahasiswa.index')->withErrors(['error' => 'Tidak dapat menghapus akun yang sedang anda gunakan']);
        }

        if ($mahasiswa->user) {
            $mahasiswa->user->delete();
        }
        $mahasiswa->delete();

        return redirect()->route('admin.mahasiswa.index')
            ->with('message', [
                'type' => 'success',
                'title' => 'Mahasiswa Berhasil Dihapus',
                'message' => 'Data mahasiswa berhasil dihapus.',
            ]);
    }

    private function applyFilters($query, Request $request){
        return $query
            ->when($request->filled('search'), fn($q) =>
                $q->where(fn($qq) =>
                    $qq->where('nama_mhs', 'like', "%{$request->search}%")
                       ->orWhere('nim', 'like', "%{$request->search}%")
                )
            )
            ->when($request->filled('status'), fn($q) =>
                $q->where('status_mhs', $request->status === 'active' ? 'aktif' : 'tidak_aktif')
            )
            ->when($request->filled('angkatan'), fn($q) =>
                $q->where('angkatan', $request->angkatan)
            )
            ->when($request->filled('gender'), fn($q) =>
                $q->where('gender', $request->gender)
            )
            ->when($request->filled('sort') && $request->filled('sortDirection'), function($q) use ($request) {
                $sortField = $request->sort;
                $sortDirection = $request->sortDirection;

                $columnMap = [
                    'name' => 'nama_mhs',
                    'nim' => 'nim',
                    'angkatan' => 'angkatan',
                    'status' => 'status_mhs',
                    'gender' => 'gender',
                    'created_at' => 'created_at',
                ];

                if (isset($columnMap[$sortField])) {
                    return $q->orderBy($columnMap[$sortField], $sortDirection);
                }

                return $q;
            }, fn($q) => $q->orderBy('created_at', 'desc'));
    }

    public function template($filename)
    {
        $path = "templates/$filename";
        return Storage::disk('public')->download($path);
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

            $role = Role::where('nama_role', 'mahasiswa')->first();
            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // Account for header and 0-based index

                // Validate required fields
                if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3]) || empty($row[4])) {
                    $errors[] = "Baris {$rowNumber}: Data tidak lengkap";
                    continue;
                }

                $name = trim($row[0]);
                $nim = trim($row[1]);
                $email = trim($row[2]);
                $angkatan = (int) $row[3];
                $gender = strtoupper(trim($row[4]));

                // Check if NIM already exists
                if (User::where('nomor_induk', $nim)->exists()) {
                    $errors[] = "Baris {$rowNumber}: NIM {$nim} sudah terdaftar";
                    continue;
                }

                // Check if email already exists
                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris {$rowNumber}: Email {$email} sudah terdaftar";
                    continue;
                }

                // Validate gender
                if (!in_array($gender, ['L', 'P'])) {
                    $errors[] = "Baris {$rowNumber}: Gender harus L atau P";
                    continue;
                }

                try {
                    $user = User::create([
                        'nama' => $name,
                        'email' => $email,
                        'nomor_induk' => $nim,
                        'password' => bcrypt($nim),
                        'role_id' => $role->id,
                        'first_login' => 0,
                        'is_active' => true,
                    ]);

                    Mahasiswa::create([
                        'user_id' => $user->id,
                        'nim' => $nim,
                        'nama_mhs' => $name,
                        'angkatan' => $angkatan,
                        'gender' => $gender,
                        'status_mhs' => 'aktif',
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: Error - " . $e->getMessage();
                }
            }

            $message = "Berhasil mengimpor {$imported} mahasiswa";
            if (!empty($errors)) {
                $message .= ". Terdapat " . count($errors) . " error.";
            }

            return redirect()->back()->with('message', [
                'type' => $imported > 0 ? 'success' : 'error',
                'title' => 'Import Mahasiswa',
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
