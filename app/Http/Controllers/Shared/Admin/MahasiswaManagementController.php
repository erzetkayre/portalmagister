<?php

namespace App\Http\Controllers\Shared\Admin;

use App\Http\Controllers\Concerns\ResolvesInstitutionModels;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MahasiswaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MahasiswaManagementController extends Controller
{
    use ResolvesInstitutionModels;

    private function dosenOptions(): \Illuminate\Support\Collection
    {
        return ($this->institutionModel('dosen'))::select('id', 'nama_dsn')
            ->where('status_dsn', 'aktif')
            ->orderBy('nama_dsn')
            ->get()
            ->map(fn($d) => ['id' => $d->id, 'label' => $d->nama_dsn]);
    }

    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $model   = $this->institutionModel('mahasiswa');

        $mahasiswa = $model::query()
            ->applyFilters($filters)
            ->applySorting($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn($m) => $m->toSearchResult());

        $angkatanOptions = $model::select('angkatan')
            ->whereNotNull('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan')
            ->map(fn($a) => ['value' => (string) $a, 'label' => (string) $a])
            ->values();

        return Inertia::render('shared/admin/mahasiswa/Index', [
            'mahasiswa'       => $mahasiswa,
            'filters'         => $filters,
            'dosenOptions'    => $this->dosenOptions(),
            'angkatanOptions' => $angkatanOptions,
        ]);
    }

    public function show($id)
    {
        $mhs = ($this->institutionModel('mahasiswa'))::with('pemAkademik')->findOrFail($id);

        return Inertia::render('shared/admin/mahasiswa/Show', [
            'mahasiswa' => [
                'id'           => $mhs->id,
                'nama_mhs'     => $mhs->nama_mhs,
                'nim'          => $mhs->nim,
                'angkatan'     => $mhs->angkatan,
                'gender'       => $mhs->gender,
                'status_mhs'   => $mhs->status_mhs,
                'ipk'          => $mhs->ipk,
                'sks'          => $mhs->sks,
                'pem_akademik' => $mhs->pemAkademik?->nama_dsn,
                'created_at'   => $mhs->created_at->format('j F Y'),
            ],
        ]);
    }

    public function edit($id)
    {
        $mhs = ($this->institutionModel('mahasiswa'))::findOrFail($id);

        return Inertia::render('shared/admin/mahasiswa/Edit', [
            'mahasiswa'    => $mhs,
            'dosenOptions' => $this->dosenOptions(),
        ]);
    }

    public function update(MahasiswaRequest $request, $id)
    {
        $mhs = ($this->institutionModel('mahasiswa'))::findOrFail($id);
        $mhs->update($request->validated());

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', [
                'title'       => 'Data Diperbarui',
                'description' => "Data {$mhs->nama_mhs} berhasil diperbarui.",
            ]);
    }

    public function assignPemAkademik(Request $request, $id)
    {
        $conn = Auth::user()->getDbConnection();

        $request->validate([
            'pem_akademik' => "required|integer|exists:{$conn}.ref_dosen,id",
        ]);

        $mhs = ($this->institutionModel('mahasiswa'))::findOrFail($id);
        $mhs->update(['pem_akademik' => $request->validated()['pem_akademik']]);

        return redirect()->back()->with('success', [
            'title'       => 'Pembimbing Akademik Ditambahkan',
            'description' => "Pembimbing akademik {$mhs->nama_mhs} berhasil diatur.",
        ]);
    }

    public function destroy($id)
    {
        $mhs = ($this->institutionModel('mahasiswa'))::findOrFail($id);
        $mhs->delete();

        return redirect()->back()->with('success', [
            'title'       => 'Mahasiswa Dihapus',
            'description' => "Data {$mhs->nama_mhs} berhasil dihapus.",
        ]);
    }

    private function filters(Request $request): array
    {
        return [
            'search'         => $request->input('search', ''),
            'status_mhs'     => $request->input('status_mhs'),
            'angkatan'       => $request->input('angkatan'),
            'sort_by'        => in_array($request->input('sort_by'), ['nim', 'angkatan', 'status_mhs', 'created_at'])
                                    ? $request->input('sort_by') : 'nim',
            'sort_direction' => in_array($request->input('sort_direction'), ['asc', 'desc'])
                                    ? $request->input('sort_direction') : 'asc',
            'per_page'       => min(max((int) $request->input('per_page', 10), 5), 50),
        ];
    }
}
