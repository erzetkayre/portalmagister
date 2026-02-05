<?php

namespace App\Http\Controllers\Shared\Admin;

use App\Models\Role;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Notifications\ResetPassword;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $query = User::query()->with('roles')
            ->byStudyProgram(Auth::user()->study_program_id)
            ->filterRole($filters['role'])
            ->applyFilters($filters)
            ->applySorting($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn($user) => $user->toSearchResult());
        $roles = Role::select('id', 'role_name')->orderBy('role_name')->get();
        // dd($request);
        return Inertia::render('shared/admin/user/Index',[
            'users' => $query,
            'filters' => $filters,
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        $roles = Role::select('id', 'role_name')->orderBy('role_name')->get();
        return Inertia::render('shared/admin/user/Create',[
            'roles' => $roles,
        ]);
    }

    public function store(UserRequest $request)
    {
        $user = Auth::user()->study_program_id;
        $validatedData = $request->validated();
        
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return Inertia::Render('shared/admin/user/Show',[
            'user' => $user,
        ]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id)->load('roles');
        $roles = Role::all();

        return Inertia::Render('shared/admin/user/Edit',[
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(UserRequest $request, $id)
    {
        $validatedData = $request->validated();
        $user = User::findOrFail($id);
        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'nomor_induk' => $validatedData['nomor_induk'],
            'phone' => $validatedData['phone'] ?? null,
            'is_active' => $validatedData['is_active'],
        ]);

        $user->roles()->sync($validatedData['roles'] ?? []);

        return redirect()->intended(route('admin.users.index',absolute:false))
            ->with('success', [
                'title' => 'Update Berhasil',
                'description' => "Informasi user $user->name berhasil diperbarui."
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if($user->id == Auth::user()->id){
            return redirect()->back()->with('error', [
                'title' => 'Hapus User Gagal',
                'description' => "Tidak bisa menghapus akun sendiri"
            ]);
        }

        $user->delete();
        return redirect()->back()->with('success', [
            'title' => 'User Berhasil Dihapus',
            'description' => "akun $user->name berhasil dihapus."
        ]);
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($user->nomor_induk),
            'first_login' => false,
            'remember_token' => null,
        ]);

        return redirect()->back()->with('success', [
            'title' => 'Reset Password Berhasil',
            'description' => "Password $user->name berhasil direset."
        ]);
    }

    public function filters(Request $request)
    {
        return [
            'search' => $request->input('search', ''),
            'role' => $request->input('role'),
            'sort_by' => in_array(
                $request->input('sort_by'),
                ['name', 'nomor_induk','email', 'is_active', 'created_at']
            ) ? $request->input('sort_by') : 'created_at',

            'sort_direction' => in_array(
                $request->input('sort_direction'),
                ['asc', 'desc']
            ) ? $request->input('sort_direction') : 'asc',

            'per_page' => min(
                max((int) $request->input('per_page', 10), 5),
                50
            ),
        ];
    }
}
