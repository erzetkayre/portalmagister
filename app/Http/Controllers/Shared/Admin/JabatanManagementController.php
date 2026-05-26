<?php

namespace App\Http\Controllers\Shared\Admin;

use App\Http\Controllers\Concerns\ResolvesInstitutionModels;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\JabatanRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JabatanManagementController extends Controller
{
    use ResolvesInstitutionModels;

    public function index(Request $request)
    {
        $filters      = $this->filters($request);
        $jabatanModel = $this->institutionModel('jabatan');
        $dosenModel   = $this->institutionModel('dosen');

        $jabatan = $jabatanModel::query()
            ->with('dosen')
            ->applyFilters($filters)
            ->applySorting($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn($j) => $j->toSearchResult());

        $dosen = $dosenModel::where('status_dsn', 'aktif')
            ->orderBy('nama_dsn')
            ->get(['id', 'nama_dsn']);

        return Inertia::render('shared/admin/jabatan/Index', [
            'jabatan' => $jabatan,
            'filters' => $filters,
            'dosen'   => $dosen,
        ]);
    }

    public function store(JabatanRequest $request)
    {
        $jabatan = ($this->institutionModel('jabatan'))::create($request->validated());

        return redirect()->route('admin.jabatan.index')
            ->with('success', [
                'title'       => 'Jabatan Ditambahkan',
                'description' => "Jabatan {$jabatan->nama_jabatan} berhasil disimpan.",
            ]);
    }

    public function update(JabatanRequest $request, $id)
    {
        $jabatan = ($this->institutionModel('jabatan'))::findOrFail($id);
        $jabatan->update($request->validated());

        return redirect()->route('admin.jabatan.index')
            ->with('success', [
                'title'       => 'Jabatan Diperbarui',
                'description' => "Jabatan {$jabatan->nama_jabatan} berhasil diperbarui.",
            ]);
    }

    public function destroy($id)
    {
        $jabatan = ($this->institutionModel('jabatan'))::findOrFail($id);
        $jabatan->delete();

        return redirect()->back()
            ->with('success', [
                'title'       => 'Jabatan Dihapus',
                'description' => "Jabatan {$jabatan->nama_jabatan} berhasil dihapus.",
            ]);
    }

    private function filters(Request $request): array
    {
        return [
            'search'         => $request->input('search', ''),
            'sort_by'        => in_array($request->input('sort_by'), ['nama_jabatan', 'created_at'])
                                    ? $request->input('sort_by') : 'nama_jabatan',
            'sort_direction' => in_array($request->input('sort_direction'), ['asc', 'desc'])
                                    ? $request->input('sort_direction') : 'asc',
            'per_page'       => min(max((int) $request->input('per_page', 10), 5), 50),
        ];
    }
}
