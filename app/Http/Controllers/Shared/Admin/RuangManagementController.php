<?php

namespace App\Http\Controllers\Shared\Admin;

use App\Http\Controllers\Concerns\ResolvesInstitutionModels;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RuangRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RuangManagementController extends Controller
{
    use ResolvesInstitutionModels;

    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $model   = $this->institutionModel('ruang');

        $ruang = $model::query()
            ->applyFilters($filters)
            ->applySorting($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn($r) => $r->toSearchResult());

        return Inertia::render('shared/admin/ruang/Index', [
            'ruang'   => $ruang,
            'filters' => $filters,
        ]);
    }

    public function store(RuangRequest $request)
    {
        $ruang = ($this->institutionModel('ruang'))::create($request->validated());

        return redirect()->route('admin.ruang.index')
            ->with('success', [
                'title'       => 'Ruang Ditambahkan',
                'description' => "Ruang {$ruang->nama_ruang} berhasil disimpan.",
            ]);
    }

    public function update(RuangRequest $request, $id)
    {
        $ruang = ($this->institutionModel('ruang'))::findOrFail($id);
        $ruang->update($request->validated());

        return redirect()->route('admin.ruang.index')
            ->with('success', [
                'title'       => 'Ruang Diperbarui',
                'description' => "Ruang {$ruang->nama_ruang} berhasil diperbarui.",
            ]);
    }

    public function destroy($id)
    {
        $ruang = ($this->institutionModel('ruang'))::findOrFail($id);
        $ruang->delete();

        return redirect()->back()
            ->with('success', [
                'title'       => 'Ruang Dihapus',
                'description' => "Ruang {$ruang->nama_ruang} berhasil dihapus.",
            ]);
    }

    private function filters(Request $request): array
    {
        return [
            'search'         => $request->input('search', ''),
            'sort_by'        => in_array($request->input('sort_by'), ['nama_ruang', 'created_at'])
                                    ? $request->input('sort_by') : 'nama_ruang',
            'sort_direction' => in_array($request->input('sort_direction'), ['asc', 'desc'])
                                    ? $request->input('sort_direction') : 'asc',
            'per_page'       => min(max((int) $request->input('per_page', 10), 5), 50),
        ];
    }
}
