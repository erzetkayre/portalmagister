<?php

namespace App\Http\Controllers\Shared\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Notifications\ResetPassword;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $query = User::query()->byStudyProgram(Auth::user()->study_program_id)
            ->applyFilters($filters)
            ->applySorting($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn($user) => $user->toSearchResult());

        // dd($request);
        return Inertia::render('shared/admin/user/Index',[
            'users' => $query,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if($user->id == Auth::user()->id){
            return redirect()->back()->with('error', [
                'title' => 'Reset Password Gagal',
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
        $user->password = Hash::make($user->nomor_induk);
        $user->save();

        return redirect()->back()->with('success', [
            'title' => 'Reset Password Berhasil',
            'description' => "Password $user->name berhasil direset."
        ]);
    }

    public function filters(Request $request)
    {
        return [
            'search' => $request->input('search', ''),
            'is_active' => $request->input('is_active'),
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
