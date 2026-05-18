<?php

namespace App\Http\Controllers\Shared\Admin;

use App\Http\Controllers\Concerns\ResolvesInstitutionModels;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserManagementController extends Controller
{
    use ResolvesInstitutionModels;

    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $users   = User::query()->with('roles')
            ->byStudyProgram(Auth::user()->study_program_id)
            ->filterRole($filters['role'])
            ->applyFilters($filters)
            ->applySorting($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn($u) => $u->toSearchResult());

        return Inertia::render('shared/admin/user/Index', [
            'users'   => $users,
            'filters' => $filters,
            'roles'   => Role::select('id', 'role_name')->orderBy('role_name')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('shared/admin/user/Create', [
            'roles' => Role::select('id', 'role_name')->orderBy('role_name')->get(),
        ]);
    }

    public function store(UserRequest $request)
    {
        $v         = $request->validated();
        $roleNames = Role::whereIn('id', $v['roles'])->pluck('role_name');
        $mainConn  = DB::connection('main');
        $instConn  = DB::connection(Auth::user()->getDbConnection());

        $mainConn->beginTransaction();
        $instConn->beginTransaction();

        try {
            $user = User::create([
                'name'             => $v['name'],
                'email'            => $v['email'],
                'nomor_induk'      => $v['nomor_induk'],
                'phone'            => $v['phone'] ?? null,
                'is_active'        => $v['is_active'],
                'study_program_id' => Auth::user()->study_program_id,
                'password'         => Hash::make($v['nomor_induk']),
                'first_login'      => false,
            ]);

            $user->roles()->sync($v['roles']);

            if ($roleNames->contains('mahasiswa')) {
                ($this->institutionModel('mahasiswa'))::create([
                    'user_id'    => $user->id,
                    'nim'        => $v['nomor_induk'],
                    'nama_mhs'   => $v['name'],
                    'status_mhs' => 'aktif',
                ]);
            } else {
                ($this->institutionModel('dosen'))::create([
                    'user_id'    => $user->id,
                    'nip'        => $v['nomor_induk'],
                    'nama_dsn'   => $v['name'],
                    'status_dsn' => 'aktif',
                ]);
            }

            $mainConn->commit();
            $instConn->commit();
        } catch (\Throwable $e) {
            $mainConn->rollBack();
            $instConn->rollBack();
            throw $e;
        }

        return redirect()->route('admin.users.index')
            ->with('success', [
                'title'       => 'User Berhasil Ditambahkan',
                'description' => "Akun {$user->name} berhasil dibuat.",
            ]);
    }

    public function show($id)
    {
        $user = User::with('roles')->byStudyProgram(Auth::user()->study_program_id)->findOrFail($id);

        return Inertia::render('shared/admin/user/Show', [
            'user' => $user->append('photo_url'),
        ]);
    }

    public function edit($id)
    {
        $user = User::with('roles')->byStudyProgram(Auth::user()->study_program_id)->findOrFail($id);

        return Inertia::render('shared/admin/user/Edit', [
            'user'  => $user->append('photo_url'),
            'roles' => Role::select('id', 'role_name')->orderBy('role_name')->get(),
        ]);
    }

    public function update(UserRequest $request, $id)
    {
        $v    = $request->validated();
        $user = User::byStudyProgram(Auth::user()->study_program_id)->findOrFail($id);

        $user->update([
            'name'        => $v['name'],
            'email'       => $v['email'],
            'nomor_induk' => $v['nomor_induk'],
            'phone'       => $v['phone'] ?? null,
            'is_active'   => $v['is_active'],
        ]);

        $user->roles()->sync($v['roles']);

        // keep ref_mahasiswa/ref_dosen name + nomor_induk in sync
        $roleNames = $user->roles()->pluck('role_name');
        if ($roleNames->contains('mahasiswa')) {
            ($this->institutionModel('mahasiswa'))::where('user_id', $user->id)->update([
                'nim'      => $v['nomor_induk'],
                'nama_mhs' => $v['name'],
            ]);
        } else {
            ($this->institutionModel('dosen'))::where('user_id', $user->id)->update([
                'nip'      => $v['nomor_induk'],
                'nama_dsn' => $v['name'],
            ]);
        }

        return redirect()->intended(route('admin.users.index', absolute: false))
            ->with('success', [
                'title'       => 'Update Berhasil',
                'description' => "Informasi user {$user->name} berhasil diperbarui.",
            ]);
    }

    public function destroy($id)
    {
        $user = User::byStudyProgram(Auth::user()->study_program_id)->findOrFail($id);

        if ($user->id === Auth::user()->id) {
            return redirect()->back()->with('error', [
                'title'       => 'Hapus User Gagal',
                'description' => 'Tidak bisa menghapus akun sendiri.',
            ]);
        }

        $user->delete();

        return redirect()->back()->with('success', [
            'title'       => 'User Berhasil Dihapus',
            'description' => "Akun {$user->name} berhasil dihapus.",
        ]);
    }

    public function resetPassword($id)
    {
        $user = User::byStudyProgram(Auth::user()->study_program_id)->findOrFail($id);
        $user->update([
            'password'       => Hash::make($user->nomor_induk),
            'first_login'    => false,
            'remember_token' => null,
        ]);

        return redirect()->back()->with('success', [
            'title'       => 'Reset Password Berhasil',
            'description' => "Password {$user->name} berhasil direset.",
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'type' => 'required|in:mahasiswa,dosen',
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $type           = $request->input('type');
        $reader         = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($request->file('file')->getPathname());
        $sheet          = $reader->load($request->file('file')->getPathname())->getActiveSheet();
        $rows           = $sheet->toArray(null, true, true, true);
        $roleId         = Role::where('role_name', $type === 'mahasiswa' ? 'mahasiswa' : 'dosen')->value('id');
        $studyProgramId = Auth::user()->study_program_id;
        $mainConn       = DB::connection('main');
        $instConn       = DB::connection(Auth::user()->getDbConnection());
        $errors         = [];
        $count          = 0;

        foreach (array_slice($rows, 1) as $i => $row) {
            $rowNum = $i + 2;
            $name   = trim($row['A'] ?? '');
            $nim    = trim($row['B'] ?? '');

            if (!$name || !$nim) continue;

            if (User::where('nomor_induk', $nim)->exists()) {
                $errors[] = "Baris {$rowNum}: Nomor induk {$nim} sudah terdaftar.";
                continue;
            }

            $mainConn->beginTransaction();
            $instConn->beginTransaction();

            try {
                $user = User::create([
                    'name'             => $name,
                    'email'            => trim($row['C'] ?? '') ?: strtolower(str_replace(' ', '.', $name)) . '@' . Auth::user()->studyProgram->db_connection . '.ac.id',
                    'nomor_induk'      => $nim,
                    'phone'            => trim($row['D'] ?? '') ?: null,
                    'is_active'        => true,
                    'study_program_id' => $studyProgramId,
                    'password'         => Hash::make($nim),
                    'first_login'      => false,
                ]);

                $user->roles()->attach($roleId);

                if ($type === 'mahasiswa') {
                    ($this->institutionModel('mahasiswa'))::create([
                        'user_id'    => $user->id,
                        'nim'        => $nim,
                        'nama_mhs'   => $name,
                        'angkatan'   => (int) ($row['E'] ?? 0) ?: null,
                        'gender'     => in_array($row['F'] ?? '', ['L', 'P']) ? $row['F'] : null,
                        'status_mhs' => in_array($row['G'] ?? '', ['aktif', 'tidak aktif']) ? $row['G'] : 'aktif',
                    ]);
                } else {
                    ($this->institutionModel('dosen'))::create([
                        'user_id'         => $user->id,
                        'nip'             => trim($row['B'] ?? $nim),
                        'nama_dsn'        => $name,
                        'kode_dsn'        => trim($row['E'] ?? '') ?: null,
                        'bidang_keahlian' => trim($row['F'] ?? '') ?: null,
                        'gender'          => in_array($row['G'] ?? '', ['L', 'P']) ? $row['G'] : null,
                        'status_dsn'      => in_array($row['H'] ?? '', ['aktif', 'tidak aktif']) ? $row['H'] : 'aktif',
                    ]);
                }

                $mainConn->commit();
                $instConn->commit();
                $count++;
            } catch (\Throwable) {
                $mainConn->rollBack();
                $instConn->rollBack();
                $errors[] = "Baris {$rowNum}: Gagal menyimpan.";
            }
        }

        $msg = "Berhasil mengimpor {$count} data {$type}.";
        if (!empty($errors)) {
            $msg .= ' ' . count($errors) . ' baris dilewati.';
        }

        return redirect()->back()->with('success', [
            'title'       => 'Import Berhasil',
            'description' => $msg,
        ]);
    }

    public function template(string $type): StreamedResponse
    {
        abort_unless(in_array($type, ['mahasiswa', 'dosen']), 404);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        if ($type === 'mahasiswa') {
            $headers = ['Nama', 'NIM', 'Email', 'No. Telepon', 'Angkatan', 'Jenis Kelamin (L/P)', 'Status (aktif/tidak aktif)'];
            $sample  = ['Budi Santoso', '24PWK001', 'budi@email.com', '081234567890', '2024', 'L', 'aktif'];
        } else {
            $headers = ['Nama', 'NIP', 'Email', 'No. Telepon', 'Kode Dosen', 'Bidang Keahlian', 'Jenis Kelamin (L/P)', 'Status (aktif/tidak aktif)'];
            $sample  = ['Prof. Budi Santoso', '196501011990011001', 'budi@email.com', '081234567890', 'BSD', 'Sistem Tenaga Listrik', 'L', 'aktif'];
        }

        $sheet->fromArray([$headers, $sample], null, 'A1');

        $sheet->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1')
            ->getFont()->setBold(true);

        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, "template_import_{$type}.xlsx", ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    private function filters(Request $request): array
    {
        return [
            'search'         => $request->input('search', ''),
            'role'           => $request->input('role'),
            'sort_by'        => in_array($request->input('sort_by'), ['name', 'nomor_induk', 'email', 'is_active', 'created_at'])
                                    ? $request->input('sort_by') : 'created_at',
            'sort_direction' => in_array($request->input('sort_direction'), ['asc', 'desc'])
                                    ? $request->input('sort_direction') : 'asc',
            'per_page'       => min(max((int) $request->input('per_page', 10), 5), 50),
        ];
    }
}
