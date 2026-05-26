<?php

namespace App\Http\Controllers\Shared\Admin;

use App\Http\Controllers\Concerns\ResolvesInstitutionModels;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DosenRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DosenManagementController extends Controller
{
    use ResolvesInstitutionModels;

    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $model   = $this->institutionModel('dosen');

        $dosen = $model::query()
            ->applyFilters($filters)
            ->applySorting($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn($d) => $d->toSearchResult());

        return Inertia::render('shared/admin/dosen/Index', [
            'dosen'   => $dosen,
            'filters' => $filters,
        ]);
    }

    public function show($id)
    {
        $model = $this->institutionModel('dosen');
        $dosen = $model::with('jabatan')->findOrFail($id);

        return Inertia::render('shared/admin/dosen/Show', [
            'dosen' => $dosen->toSearchResult() + [
                'jabatan' => $dosen->jabatan->map(fn($j) => ['id' => $j->id, 'nama_jabatan' => $j->nama_jabatan]),
            ],
        ]);
    }

    public function edit($id)
    {
        $dosen = ($this->institutionModel('dosen'))::findOrFail($id);

        return Inertia::render('shared/admin/dosen/Edit', [
            'dosen' => $dosen,
        ]);
    }

    public function update(DosenRequest $request, $id)
    {
        $dosen = ($this->institutionModel('dosen'))::findOrFail($id);
        $dosen->update($request->validated());

        return redirect()->route('admin.dosen.index')
            ->with('success', [
                'title'       => 'Data Diperbarui',
                'description' => "Data {$dosen->nama_dsn} berhasil diperbarui.",
            ]);
    }

    public function destroy($id)
    {
        $dosen = ($this->institutionModel('dosen'))::findOrFail($id);
        $dosen->delete();

        return redirect()->back()
            ->with('success', [
                'title'       => 'Dosen Dihapus',
                'description' => "Data {$dosen->nama_dsn} berhasil dihapus.",
            ]);
    }

    private function filters(Request $request): array
    {
        return [
            'search'         => $request->input('search', ''),
            'status_dsn'     => $request->input('status_dsn'),
            'sort_by'        => in_array($request->input('sort_by'), ['kode_dsn', 'nip', 'status_dsn', 'created_at'])
                                    ? $request->input('sort_by') : 'nip',
            'sort_direction' => in_array($request->input('sort_direction'), ['asc', 'desc'])
                                    ? $request->input('sort_direction') : 'asc',
            'per_page'       => min(max((int) $request->input('per_page', 10), 5), 50),
        ];
    }
}
