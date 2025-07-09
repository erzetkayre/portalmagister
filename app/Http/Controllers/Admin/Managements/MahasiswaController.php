<?php

namespace App\Http\Controllers\Admin\Managements;

use Inertia\Inertia;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MahasiswaController extends Controller
{
    public function index(Request $request) {
        $query = Mahasiswa::with('role')->select('id', 'nama', 'email', 'role_id', 'is_active', 'created_at');
        $query = $this->applyFilters($query, $request);

        $users = $query->paginate(10)->through(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->nama_mhs,
                'nim' => $user->nim,
                'angkatan' => $user->angkatan,
                'status' => $user->is_active ? 'active' : 'inactive',
                'gender' => $user->gender,
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

    }

    public function store(Request $request) {

    }

    public function show($id) {

    }

    public function edit($id) {

    }

    public function update(Request $request, $id) {

    }

    public function destroy($id) {

    }

    private function applyFilters($query, Request $request){
        return $query
            ->when($request->filled('search'), fn($q) =>
                $q->where(fn($qq) =>
                    $qq->where('nama', 'like', "%{$request->search}%")
                       ->orWhere('nim', 'like', "%{$request->search}%")
                )
            )
            ->when($request->filled('role'), fn($q) =>
                $q->whereHas('role', fn($qq) =>
                    $qq->where('nama_role', $request->role)
                )
            )
            ->when($request->filled('role'), fn($q) =>
                $q->whereHas('role', fn($qq) =>
                    $qq->where('nama_role', $request->role)
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        // Handle Excel import logic here
        // You can use Laravel Excel package or other Excel processing libraries

        return redirect()->back()->with('success', 'Users imported successfully!');
    }
}
