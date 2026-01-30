<?php

namespace App\Http\Controllers\Shared\Admin;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $query = User::query()->byStudyProgram(Auth::user()->study_program_id)->applyFilters($filters)
            ->applySorting($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn($user) => $user->toSearchResult());

        // dd($query);
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
        //
    }

    public function filters(Request $request)
    {
        return [
            'search' => $request->input('search', ''),
            'is_active' => $request->input('is_active'),
            'sort_by' => in_array(
                $request->input('sort_by'),
                ['id', 'name', 'email', 'is_active', 'created_at']
            ) ? $request->input('sort_by') : 'id',

            'sort_direction' => in_array(
                $request->input('sort_direction'),
                ['asc', 'desc']
            ) ? $request->input('sort_direction') : 'asc',

            'per_page' => min(
                max((int) $request->input('per_page', 10), 5),
                100
            ),
        ];
    }
}
