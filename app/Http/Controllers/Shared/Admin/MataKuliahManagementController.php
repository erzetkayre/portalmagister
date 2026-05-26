<?php

namespace App\Http\Controllers\Shared\Admin;

use App\Http\Controllers\Concerns\ResolvesInstitutionModels;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MataKuliahRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MataKuliahManagementController extends Controller
{
    use ResolvesInstitutionModels;

    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $model   = $this->institutionModel('mk');

        $mk = $model::query()
            ->applyFilters($filters)
            ->applySorting($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn($m) => $m->toSearchResult());

        return Inertia::render('shared/admin/mk/Index', [
            'mk'      => $mk,
            'filters' => $filters,
        ]);
    }

    public function store(MataKuliahRequest $request)
    {
        $mk = ($this->institutionModel('mk'))::create($request->validated());

        return redirect()->route('admin.mk.index')
            ->with('success', [
                'title'       => 'Mata Kuliah Ditambahkan',
                'description' => "{$mk->nama_mk} berhasil disimpan.",
            ]);
    }

    public function update(MataKuliahRequest $request, $id)
    {
        $mk = ($this->institutionModel('mk'))::findOrFail($id);
        $mk->update($request->validated());

        return redirect()->route('admin.mk.index')
            ->with('success', [
                'title'       => 'Mata Kuliah Diperbarui',
                'description' => "{$mk->nama_mk} berhasil diperbarui.",
            ]);
    }

    public function destroy($id)
    {
        $mk = ($this->institutionModel('mk'))::findOrFail($id);
        $mk->delete();

        return redirect()->back()
            ->with('success', [
                'title'       => 'Mata Kuliah Dihapus',
                'description' => "{$mk->nama_mk} berhasil dihapus.",
            ]);
    }

    private function filters(Request $request): array
    {
        return [
            'search'         => $request->input('search', ''),
            'status_mk'      => $request->input('status_mk'),
            'sort_by'        => in_array($request->input('sort_by'), ['nama_mk', 'kode_mk', 'sks', 'created_at'])
                                    ? $request->input('sort_by') : 'nama_mk',
            'sort_direction' => in_array($request->input('sort_direction'), ['asc', 'desc'])
                                    ? $request->input('sort_direction') : 'asc',
            'per_page'       => min(max((int) $request->input('per_page', 10), 5), 50),
        ];
    }
}
