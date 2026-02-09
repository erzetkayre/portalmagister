<?php

namespace App\Http\Controllers\Shared\Admin;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\PWK\Mahasiswa as MahasiswaPWK;
use App\Models\Elektro\Mahasiswa as MahasiswaElektro;

class MahasiswaManagementController extends Controller
{
    public function index(Request $request) {
        $filters = $this->filters($request);
        $model = match(Auth::user()->study_program_id) {
            'pwk' => MahasiswaPWK::class,
            'elektro' => MahasiswaElektro::class,
            default => abort(403),
        };
        $query = $model::query()
            ->applyFilters($filters)
            ->applySorting($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn($user) => $user->toSearchResult());
        // dd($request);
        return Inertia::render('shared/admin/mahasiswa/Index',[
            'mahasiswa' => $query,
            'filters' => $filters,
        ]);
    }

    public function filters(Request $request)
    {
        return [
            'search' => $request->input('search', ''),
            'sort_by' => in_array(
                $request->input('sort_by'),
                ['nama_mhs', 'nim','status', 'angkatan', 'created_at']
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
