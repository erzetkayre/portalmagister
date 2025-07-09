<?php

namespace App\Http\Controllers\Admin\Managements;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DosenController extends Controller
{
    public function index(Request $request) {
        $query = User::with('role')
            ->select('id', 'nama', 'email', 'role_id', 'is_active', 'created_at');

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('role')) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('nama_role', $request->role);
            });
        }
        if ($request->filled('status')) {
            $status = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $status);
        }

        // Paginate
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

        // Roles dropdown
        $roles = Role::select('nama_role')->distinct()->get()->pluck('nama_role');

        return Inertia::render('admin/managements/Users', [
            'users' => $users,
            'roles' => $roles,
            'filters' => [
                'search' => $request->search,
                'role' => $request->role,
                'status' => $request->status,
            ]
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
