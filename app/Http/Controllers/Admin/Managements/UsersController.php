<?php

namespace App\Http\Controllers\Admin\Managements;

use App\Models\Role;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function index(Request $request) {
        $query = User::with('role')->select('id', 'nama', 'email', 'role_id', 'is_active', 'created_at');
        $query = $this->applyFilters($query, $request);

        $users = $query->paginate(10)->through(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->nama,
                'email' => $user->email,
                'role' => $user->role->nama_role ?? 'Unknown',
                'status' => $user->is_active ? 'active' : 'inactive',
                'created_at' => $user->created_at->format('Y-m-d'),
            ];
        });

        $roles = Role::select('nama_role')->distinct()->get()->pluck('nama_role');
        $totalUsers = User::count();
        $totalAdmin = User::whereIn('role_id', [1, 2])->count();
        $totalMahasiswa = User::where('role_id', 4)->count();
        $totalDosen = User::where('role_id', 3)->count();

        return Inertia::render('admin/managements/users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => [
                'search' => $request->search,
                'role' => $request->role,
                'status' => $request->status,
            ],
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalAdmin' => $totalAdmin,
                'totalMahasiswa' => $totalMahasiswa,
                'totalDosen' => $totalDosen,
            ],
        ]);
    }

    public function create() {
        $roles = Role::select('id', 'nama_role', 'deskripsi')->get();

        return Inertia::render('admin/managements/users/Create', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nomor_induk' => 'required|string|max:255|unique:users,nomor_induk',
            'role' => 'required|exists:roles,nama_role',
        ]);

        $role = Role::where('nama_role', $request->role)->first();

        $user = User::create([
            'nama' => $request->name,
            'email' => $request->email,
            'nomor_induk' => $request->nomor_induk,
            'password' => bcrypt($request->nomor_induk),
            'role_id' => $role->id,
            'first_login' => 0,
            'is_active' => true,
        ]);

        if ($role->nama_role === 'mahasiswa') {
            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => (string)$request->nomor_induk,
                'nama_mhs' => (string)$request->name,
                'angkatan' => (int)date('Y'),
                'status_mhs' => 'aktif',
            ]);
        } elseif (in_array($role->nama_role, ['koordinator', 'dosen', 'admin'])) {
            Dosen::create([
                'user_id' => $user->id,
                'nip' => (string)$request->nomor_induk,
                'kode_dosen' => (string)$request->nomor_induk, // Default sama dengan NIP
                'nama_dosen' => (string)$request->name,
                'status_dosen' => 'aktif',
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('message', [
                    'type' => 'success',
                    'title' => 'User Berhasil Dibuat',
                    'message' => 'User baru telah ditambahkan ke sistem.',
            ]);
    }

    public function show($id) {
        $user = User::with('role')->findOrFail($id);

        return Inertia::render('admin/managements/users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->nama,
                'email' => $user->email,
                'nomor_induk' => $user->nomor_induk,
                'role' => $user->role->nama_role ?? 'Unknown',
                'status' => $user->is_active ? 'active' : 'inactive',
                'first_login' => $user->first_login,
                'created_at' => $user->created_at->format('Y-m-d'),
            ]
        ]);
    }

    public function edit($id) {
        $user = User::with('role')->findOrFail($id);
        $roles = Role::select('id', 'nama_role', 'deskripsi')->get();

        return Inertia::render('admin/managements/users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->nama,
                'email' => $user->email,
                'nomor_induk' => $user->nomor_induk,
                'role' => $user->role->nama_role ?? '',
                'is_active' => $user->is_active,
            ],
            'roles' => $roles
        ]);
    }

    public function update(Request $request, $id) {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nomor_induk' => 'required|string|max:255|unique:users,nomor_induk,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,nama_role',
            'is_active' => 'boolean',
        ]);

        $role = Role::where('nama_role', $request->role)->first();

        $updateData = [
            'nama' => $request->name,
            'email' => $request->email,
            'nomor_induk' => $request->nomor_induk,
            'role_id' => $role->id,
            'is_active' => (bool) $request->is_active,
        ];

        if ($request->password) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('message', [
                'type' => 'success',
                'title' => 'User Berhasil Diperbarui',
                'message' => 'Data user berhasil diperbarui.',
            ]);
    }

    public function destroy($id) {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->withErrors(['error' => 'Tidak dapat menghapus akun yang sedang anda gunakan']);
        }

        $user->delete();

        return redirect()->route('admin.users.index');
    }

    private function applyFilters($query, Request $request){
        return $query
            ->when($request->filled('search'), fn($q) =>
                $q->where(fn($qq) =>
                    $qq->where('nama', 'like', "%{$request->search}%")
                       ->orWhere('email', 'like', "%{$request->search}%")
                )
            )
            ->when($request->filled('role'), fn($q) =>
                $q->whereHas('role', fn($qq) =>
                    $qq->where('nama_role', $request->role)
                )
            )
            ->when($request->filled('status'), fn($q) =>
                $q->where('is_active', $request->status === 'active' ? 1 : 0)
            );
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        $newPassword = $user->nomor_induk;
        $user->update([
            'password' => bcrypt($newPassword),
            'first_login' => 0,
        ]);

        return redirect()->route('admin.users.index')->with('message', [
            'type' => 'success',
            'title' => 'Password Berhasil Direset',
            'message' => 'Password user telah direset ke nomor induk dan harus diganti saat login berikutnya.',
        ]);
    }
}
