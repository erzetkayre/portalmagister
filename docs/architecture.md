# Arsitektur Sistem — portalmagister

> Laravel + Vue + Inertia.js | S2 Thesis Management System  
> Dua Institusi: Elektro & PWK

---

## Daftar Isi

1. [Gambaran Umum Sistem](#1-gambaran-umum-sistem)
2. [Struktur Backend (Laravel)](#2-struktur-backend-laravel)
3. [Domain Modeling](#3-domain-modeling)
4. [Multi-Institusi](#4-multi-institusi)
5. [Desain Database](#5-desain-database)
6. [Alur Controller → Inertia → Vue](#6-alur-controller--inertia--vue)
7. [Struktur Frontend (Vue)](#7-struktur-frontend-vue)
8. [Strategi Migrasi dari Legacy](#8-strategi-migrasi-dari-legacy)
9. [Coding Guidelines](#9-coding-guidelines)
10. [Menghindari Overengineering](#10-menghindari-overengineering)

---

## 1. Gambaran Umum Sistem

### Stack

| Layer     | Teknologi                          |
|-----------|------------------------------------|
| Backend   | Laravel 12                         |
| Frontend  | Vue 3 + Inertia.js                 |
| UI        | shadcn-vue (reka-ui)               |
| Database  | MySQL (multi-connection)           |
| Auth      | Laravel session (bukan token/JWT)  |

### Alur Umum Inertia

```
HTTP Request
    → Laravel Controller
        → Inertia::render('path/to/Page', [...data])
            → Vue Page Component (SSR-aware)
```

Tidak ada REST API terpisah. Controller Laravel langsung merender Vue page via Inertia.

### Dua Institusi

| Institusi | StudyProgram | DB Connection | DB Name       |
|-----------|--------------|---------------|---------------|
| Elektro   | elektro      | `elektro`     | `dbmelektro`  |
| PWK       | pwk          | `pwk`         | `dbmpwk`      |
| Shared    | —            | `main`        | `dbportalmagister` |

`StudyProgram.db_connection` menentukan database mana yang dipakai. `StudyProgramMiddleware` memproteksi route per institusi.

### Siklus Tesis

```
Pendaftaran → Pembimbingan / Logbook → Seminar → Pendadaran → Selesai
  (draft)       (supervision)         (seminar)   (defense)   (completed)
```

---

## 2. Struktur Backend (Laravel)

### Struktur Direktori `app/`

```
app/
├── Helpers/
│   ├── Filterable.php          # trait query filtering
│   └── Sortable.php            # trait query sorting
│
├── Http/
│   ├── Controllers/
│   │   ├── Auth/               # Login, FirstLogin, Profile
│   │   ├── Shared/
│   │   │   └── Admin/          # UserManagement, MahasiswaManagement
│   │   ├── Elektro/
│   │   │   ├── Admin/          # ThesisManagement, SeminarManagement, DefenseManagement
│   │   │   ├── Mahasiswa/      # MyThesis, ThesisRegistration, Logbook
│   │   │   └── Dosen/          # SupervisionList, LogbookApproval
│   │   └── PWK/
│   │       ├── Admin/
│   │       ├── Mahasiswa/
│   │       └── Dosen/
│   │
│   ├── Middleware/
│   │   ├── StudyProgramMiddleware.php
│   │   └── FirstLoginMiddleware.php
│   │
│   └── Requests/
│       ├── Admin/
│       │   └── UserRequest.php
│       └── Thesis/
│           ├── ThesisRegistrationRequest.php
│           ├── ThesisApprovalRequest.php
│           ├── LogbookRequest.php
│           ├── SeminarRequest.php
│           └── DefenseRequest.php
│
└── Models/
    ├── User.php                # main DB
    ├── Role.php                # main DB
    ├── StudyProgram.php        # main DB
    ├── Elektro/
    │   ├── Mahasiswa.php
    │   ├── Dosen.php
    │   ├── Thesis.php
    │   ├── ThesisSupervisor.php
    │   ├── ThesisLogbook.php
    │   ├── ThesisSeminar.php
    │   ├── ThesisSeminarExaminer.php
    │   ├── ThesisDefense.php
    │   └── ThesisDocument.php
    └── PWK/
        └── (struktur sama dengan Elektro)
```

### Tanggung Jawab Controller

Controller hanya boleh melakukan 3 hal:

1. Validasi input (via Form Request)
2. Memanggil model / service
3. Mengembalikan Inertia response atau redirect

```php
// BAIK — controller tipis
class ThesisController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filters($request);

        $theses = Thesis::query()
            ->with(['mahasiswa', 'supervisors.dosen'])
            ->applyFilters($filters)
            ->latest()
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn($t) => $t->toListItem());

        return Inertia::render('elektro/admin/thesis/Index', [
            'theses' => $theses,
            'filters' => $filters,
        ]);
    }

    public function store(ThesisRegistrationRequest $request)
    {
        $thesis = Thesis::create($request->validated());
        $thesis->assignSupervisors($request->supervisors);

        return redirect()->route('elektro.admin.thesis.index')
            ->with('success', [
                'title'       => 'Pendaftaran Berhasil',
                'description' => "Tesis {$thesis->mahasiswa->nama_mhs} berhasil didaftarkan.",
            ]);
    }
}
```

```php
// BURUK — controller gemuk (jangan tiru pola legacy)
public function update(Request $request, $id)
{
    switch ($request->input('action')) {
        case 'setuju':
            DB::table('ta')->where('id', $id)->update([...]);
            DB::table('pembimbing')->where('ta_id', $id)->update([...]);
            // ... dst
    }
}
```

### Kapan Membuat Service Class

Buat service **hanya** jika logika yang sama dipanggil dari beberapa controller berbeda.

| Kasus | Perlu Service? |
|-------|----------------|
| Logic yang hanya dipakai 1 controller | Tidak — taruh di model atau controller |
| Logic yang dipakai admin + mahasiswa controller | Ya — buat service |
| Notifikasi email saat approval | Ya — `ThesisNotificationService` |
| Transisi status tesis | Tidak — method di model cukup |

---

## 3. Domain Modeling

### Relasi Utama

```
User ──1──1── Mahasiswa    (user_id FK)
User ──1──1── Dosen        (user_id FK)

Mahasiswa ──1──N── Thesis
Thesis    ──1──N── ThesisSupervisor  (max 2: utama + co)
Thesis    ──1──N── ThesisLogbook
Thesis    ──1──1── ThesisSeminar
Thesis    ──1──N── ThesisSeminarExaminer  (via ThesisSeminar)
Thesis    ──1──1── ThesisDefense
Thesis    ──1──N── ThesisDocument
```

### Model: Thesis

```php
class Thesis extends Model
{
    protected $connection = 'elektro';
    protected $table = 'theses';

    protected $fillable = [
        'mahasiswa_id',
        'thesis_type',   // 'penelitian' | 'reguler'
        'title',
        'abstract',
        'status',        // lihat enum di bawah
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status lifecycle:
    // draft → registered → supervision → seminar → defense → completed
    //                    ↘ rejected (dari status apapun)

    public function mahasiswa()    { return $this->belongsTo(Mahasiswa::class); }
    public function supervisors()  { return $this->hasMany(ThesisSupervisor::class); }
    public function logbooks()     { return $this->hasMany(ThesisLogbook::class); }
    public function seminar()      { return $this->hasOne(ThesisSeminar::class); }
    public function defense()      { return $this->hasOne(ThesisDefense::class); }
    public function documents()    { return $this->hasMany(ThesisDocument::class); }

    // Status transitions — logika domain di model, bukan controller
    public function approve(): void
    {
        $this->update(['status' => 'supervision', 'started_at' => now()]);
    }

    public function reject(string $note = null): void
    {
        $this->update(['status' => 'rejected']);
    }

    // Untuk Inertia list view — data minimal
    public function toListItem(): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'thesis_type'  => $this->thesis_type,
            'status'       => $this->status,
            'mahasiswa'    => [
                'id'       => $this->mahasiswa->id,
                'nim'      => $this->mahasiswa->nim,
                'nama_mhs' => $this->mahasiswa->nama_mhs,
            ],
            'started_at'   => $this->started_at?->format('j F Y'),
            'created_at'   => $this->created_at->format('j F Y'),
        ];
    }
}
```

---

## 4. Multi-Institusi

### Pendekatan: Namespace + DB Separation (sudah ada)

Tidak perlu `institution_id` column atau strategy pattern. Pemisahan sudah ada via:

- Namespace model: `App\Models\Elektro\*` vs `App\Models\PWK\*`
- Route file terpisah: `routes/elektro.php` vs `routes/pwk.php`
- Halaman terpisah: `pages/elektro/` vs `pages/pwk/`
- Controller terpisah: `Controllers/Elektro/` vs `Controllers/PWK/`

### Ketika Kedua Institusi Punya Logic Berbeda

Cukup implementasikan berbeda di namespace masing-masing:

```php
// Elektro: workflow standar
class Elektro\Admin\ThesisController extends Controller
{
    public function approve(Thesis $thesis)
    {
        $thesis->approve(); // standard flow
        return redirect()->back()->with('success', [...]);
    }
}

// PWK: mungkin perlu approval berlapis (belum diketahui)
class PWK\Admin\ThesisController extends Controller
{
    public function approve(Thesis $thesis)
    {
        // bisa berbeda jika kebutuhan PWK berbeda
    }
}
```

### Ketika Kedua Institusi Punya Logic Sama

Ekstrak ke abstract base controller atau trait — **hanya jika duplikasi sudah nyata**:

```php
abstract class BaseThesisController extends Controller
{
    abstract protected function model(): string; // FQCN of Thesis model

    public function index(Request $request)
    {
        $model   = $this->model();
        $filters = $this->filters($request);
        $theses  = $model::query()
            ->with(['mahasiswa', 'supervisors'])
            ->applyFilters($filters)
            ->paginate($filters['per_page'])
            ->withQueryString()
            ->through(fn($t) => $t->toListItem());

        return Inertia::render($this->indexPage(), [
            'theses'  => $theses,
            'filters' => $filters,
        ]);
    }

    abstract protected function indexPage(): string;
}

class Elektro\Admin\ThesisController extends BaseThesisController
{
    protected function model(): string        { return \App\Models\Elektro\Thesis::class; }
    protected function indexPage(): string    { return 'elektro/admin/thesis/Index'; }
}
```

---

## 5. Desain Database

### main DB — Data Bersama

```sql
study_programs (id, program_name, db_connection, description, timestamps)

users (
  id, name, email, nomor_induk, password,
  study_program_id FK → study_programs,
  is_active, first_login, photo, phone,
  remember_token, email_verified_at,
  soft_deletes, timestamps
)

roles    (id, role_name, timestamps)
user_roles (user_id FK, role_id FK)
```

### elektro DB — Per Institusi (skema yang sama direplikasi ke pwk DB)

```sql
-- Referensi
ref_mahasiswa (
  id, user_id FK → main.users,
  nim UNIQUE, nama_mhs, angkatan,
  status_mhs DEFAULT 'aktif',
  sks, ipk, gender ENUM('L','P'),
  soft_deletes, timestamps
)

ref_dosen (
  id, user_id FK → main.users,
  kode_dosen UNIQUE, nip UNIQUE, nama_dosen,
  status_dosen DEFAULT 'aktif',
  bidang_keahlian, gender ENUM('L','P'),
  soft_deletes, timestamps
)

ref_ruang (id, nama_ruang, kapasitas, soft_deletes, timestamps)

-- Tesis
theses (
  id,
  mahasiswa_id FK → ref_mahasiswa,
  thesis_type ENUM('penelitian','reguler'),
  title, abstract,
  status ENUM('draft','registered','supervision','seminar','defense','completed','rejected')
    DEFAULT 'draft',
  started_at TIMESTAMP nullable,
  completed_at TIMESTAMP nullable,
  soft_deletes, timestamps
)

thesis_supervisors (
  id,
  thesis_id FK → theses,
  dosen_id FK → ref_dosen,
  position ENUM('utama','co'),
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  note TEXT nullable,
  timestamps
)

thesis_logbooks (
  id,
  thesis_id FK → theses,
  dosen_id FK → ref_dosen,
  log_date DATE,
  description TEXT,
  activity_type VARCHAR(100) nullable,
  student_signed_at TIMESTAMP nullable,
  supervisor_signed_at TIMESTAMP nullable,
  timestamps
)

thesis_seminars (
  id,
  thesis_id FK → theses,
  seminar_type ENUM('hasil'),          -- S2 Elektro hanya 'hasil', tidak ada seminar proposal
  scheduled_at TIMESTAMP nullable,
  room_id FK → ref_ruang nullable,
  status ENUM('scheduled','done','postponed') DEFAULT 'scheduled',
  grade DECIMAL(4,2) nullable,         -- nilai akhir agregat (dihitung dari thesis_seminar_examiners)
  note TEXT nullable,
  file_presentation VARCHAR nullable,
  file_report VARCHAR nullable,
  timestamps
)

thesis_seminar_examiners (
  id,
  thesis_seminar_id FK → thesis_seminars,
  dosen_id FK → ref_dosen,
  role ENUM('ketua','penguji','pembimbing'),
  grade DECIMAL(4,2) nullable,
  timestamps
)

thesis_defenses (
  id,
  thesis_id FK → theses,
  scheduled_at TIMESTAMP nullable,
  room_id FK → ref_ruang nullable,
  status ENUM('scheduled','done','postponed') DEFAULT 'scheduled',
  grade DECIMAL(4,2) nullable,         -- nilai akhir agregat (dihitung dari thesis_defense_examiners)
  note TEXT nullable,
  file_thesis VARCHAR nullable,
  file_plagiarism VARCHAR nullable,
  timestamps
)

thesis_defense_examiners (             -- struktur mirror dari thesis_seminar_examiners
  id,
  thesis_defense_id FK → thesis_defenses,
  dosen_id FK → ref_dosen,
  role ENUM('ketua','penguji','pembimbing'),
  grade DECIMAL(4,2) nullable,
  timestamps
)

thesis_documents (
  id,
  thesis_id FK → theses,
  document_type VARCHAR(100),   -- 'khs', 'krs', 'sk_pembimbing', dst
  file_path VARCHAR,
  uploaded_at TIMESTAMP,
  timestamps
)
```

> **Catatan:** Tidak ada tabel `tesis_draft` terpisah. State "draft" adalah nilai `status` pada tabel `theses`. Satu tabel, satu record per tesis, status melacak lifecycle.

---

## 6. Alur Controller → Inertia → Vue

### Pola Index (list + filter + pagination)

**Controller:**
```php
public function index(Request $request)
{
    $filters = $this->filters($request);

    $theses = Thesis::query()
        ->with(['mahasiswa'])
        ->applyFilters($filters)
        ->latest()
        ->paginate($filters['per_page'])
        ->withQueryString()            // <-- penting: filter survive paginasi
        ->through(fn($t) => $t->toListItem());

    return Inertia::render('elektro/admin/thesis/Index', [
        'theses'  => $theses,
        'filters' => $filters,
    ]);
}

private function filters(Request $request): array
{
    return [
        'search'         => $request->input('search', ''),
        'status'         => $request->input('status'),
        'thesis_type'    => $request->input('thesis_type'),
        'sort_by'        => in_array($request->input('sort_by'), ['title', 'created_at', 'status'])
                                ? $request->input('sort_by') : 'created_at',
        'sort_direction' => in_array($request->input('sort_direction'), ['asc', 'desc'])
                                ? $request->input('sort_direction') : 'desc',
        'per_page'       => min(max((int) $request->input('per_page', 10), 5), 50),
    ];
}
```

**Vue Page:**
```vue
<script setup lang="ts">
import { useTable } from '@/composables/useTable'
import { route } from 'ziggy-js'

interface Props {
    theses: Pagination<Thesis>
    filters: ThesisFilters
}
const props = defineProps<Props>()

const { searchQuery, filters, sortBy, sortDirection, handleSort, handlePageChange } = useTable({
    endpoint: route('elektro.admin.thesis.index'),
    initialFilters: props.filters,
})
</script>
```

### Pola Show (detail)

**Controller:**
```php
public function show(Thesis $thesis)
{
    $thesis->load(['mahasiswa', 'supervisors.dosen', 'logbooks', 'seminar.examiners', 'defense']);

    return Inertia::render('elektro/admin/thesis/Show', [
        'thesis' => $thesis,
    ]);
}
```

### Pola Form (create/edit)

**Controller:**
```php
public function create()
{
    $dosens = Dosen::aktif()->get(['id', 'nama_dosen', 'kode_dosen']);
    $mahasiswas = Mahasiswa::aktif()->belumPunyaTesis()->get(['id', 'nim', 'nama_mhs']);

    return Inertia::render('elektro/admin/thesis/Create', [
        'dosens'     => $dosens,
        'mahasiswas' => $mahasiswas,
    ]);
}

public function store(ThesisRegistrationRequest $request)
{
    $thesis = Thesis::create($request->safe()->except(['supervisor_1', 'supervisor_2']));

    $thesis->supervisors()->createMany([
        ['dosen_id' => $request->supervisor_1, 'position' => 'utama', 'status' => 'approved'],
        ['dosen_id' => $request->supervisor_2, 'position' => 'co',    'status' => 'approved'],
    ]);

    return redirect()->route('elektro.admin.thesis.show', $thesis)
        ->with('success', ['title' => 'Berhasil', 'description' => 'Tesis berhasil didaftarkan.']);
}
```

**Vue Form:**
```vue
<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

const props = defineProps<{ dosens: Dosen[], mahasiswas: Mahasiswa[] }>()

const form = useForm({
    mahasiswa_id: '',
    thesis_type: 'penelitian',
    title: '',
    abstract: '',
    supervisor_1: '',
    supervisor_2: '',
})

const submit = () => {
    form.post(route('elektro.admin.thesis.store'))
}
</script>

<template>
    <form @submit.prevent="submit">
        <FormInput label="Judul" v-model="form.title" :error="form.errors.title" />
        <!-- ... -->
        <Button type="submit" :disabled="form.processing">Simpan</Button>
    </form>
</template>
```

> **Aturan:** Selalu gunakan `useForm()` dari `@inertiajs/vue3` untuk semua form mutation. Tidak perlu axios manual. `useForm` sudah handle loading state, error display, dan reset otomatis.

---

## 7. Struktur Frontend (Vue)

```
resources/js/
│
├── pages/
│   ├── auth/
│   │   ├── Login.vue
│   │   └── ChangePassword.vue
│   │
│   ├── shared/
│   │   └── admin/
│   │       ├── user/           # Index, Create, Edit, Show
│   │       ├── mahasiswa/      # Index, Show
│   │       └── dosen/          # Index, Show
│   │
│   ├── elektro/
│   │   ├── Dashboard.vue
│   │   ├── admin/
│   │   │   ├── thesis/         # Index, Show, Edit
│   │   │   ├── seminar/        # Index, Show, Schedule
│   │   │   └── defense/        # Index, Show, Schedule
│   │   ├── mahasiswa/
│   │   │   ├── thesis/         # MyThesis, Register, Logbook
│   │   │   └── seminar/        # MySeminar
│   │   └── dosen/
│   │       └── supervision/    # MyStudents, LogbookApproval
│   │
│   └── pwk/
│       └── (struktur sama dengan elektro/)
│
├── components/
│   ├── ui/                     # shadcn-vue primitives (jangan diubah)
│   ├── DataTable.vue
│   ├── FilterTable.vue
│   ├── FormInput.vue
│   ├── FormSelect.vue
│   ├── ConfirmDialog.vue
│   ├── Heading.vue
│   └── thesis/                 # komponen domain thesis
│       ├── ThesisStatusBadge.vue
│       ├── ThesisSupervisorCard.vue
│       └── LogbookEntry.vue
│
├── layouts/
│   ├── AppLayout.vue
│   └── AuthLayout.vue
│
├── composables/
│   ├── useTable.ts             # sudah ada
│   └── useThesisStatus.ts      # label, warna, transisi per status
│
└── types/
    ├── index.d.ts
    └── thesis.d.ts             # Thesis, Supervisor, Logbook, Seminar, Defense
```

### Aturan Komponen

| Kondisi | Keputusan |
|---------|-----------|
| Dipakai di 2+ halaman | Taruh di `components/` |
| Dipakai di 1 halaman | Inline di page, atau file terpisah di samping page |
| Hanya display (tidak ada logic) | Bisa inline di template |
| Ada logic kompleks | Ekstrak ke composable atau komponen |

### Typing Props

Selalu typing props dengan TypeScript interface. Ini dokumentasi gratis sekaligus type safety:

```typescript
// types/thesis.d.ts
export interface Thesis {
    id: number
    thesis_type: 'penelitian' | 'reguler'
    title: string
    abstract: string | null
    status: ThesisStatus
    mahasiswa: Mahasiswa
    supervisors: ThesisSupervisor[]
    started_at: string | null
    created_at: string
}

export type ThesisStatus =
    | 'draft'
    | 'registered'
    | 'supervision'
    | 'seminar'
    | 'defense'
    | 'completed'
    | 'rejected'
```

---

## 8. Strategi Migrasi dari Legacy

### Prinsip

**Pahami aturan bisnis, jangan salin kodenya.**

Legacy `portalelektro` punya banyak legacy code: fat controllers, raw DB queries, switch statements, multiple status columns. Ambil *aturan bisnis*-nya, implementasikan ulang dengan pola yang bersih.

### Pemetaan Legacy → Baru

| Legacy (TA)              | Baru (Tesis)                          |
|--------------------------|---------------------------------------|
| `ta.status_ta = SETUJU`  | `theses.status = 'registered'`        |
| `ta.proses_ta = 2`       | Tidak perlu — status sudah cukup      |
| `pembimbing.status_pem`  | `thesis_supervisors.status`           |
| `koordinator_kbk`        | Tidak ada padanan — koordinator hanya role user |
| `ta/pendaftaran`         | `theses` + `thesis_supervisors`       |
| `logbookta`              | `thesis_logbooks`                     |
| `semhas`                 | `thesis_seminars` (type = 'hasil')    |
| `pendadaran`             | `thesis_defenses`                     |
| `tesis_draft` (bckp/)    | Tidak perlu — gunakan `status = 'draft'` |

### Yang Harus Diabaikan dari Legacy

- KP (Kerja Praktek) — bukan scope
- Capstone — bukan scope
- Analisis ML / train data — bukan scope
- Alumni — bukan scope
- Export Excel yang kompleks — implementasikan sederhana dulu bila perlu

### Migrasi Data

Bangun sistem baru dengan data bersih. Tulis script migrasi data **di akhir**, setelah sistem selesai dibangun. Jangan biarkan struktur data lama mendrive keputusan desain.

---

## 9. Coding Guidelines

### Laravel — Form Requests

Satu Form Request per aksi, bukan per model:

```php
// BAIK
ThesisRegistrationRequest   // untuk pendaftaran baru
ThesisApprovalRequest       // untuk approve/reject

// KURANG BAIK
ThesisRequest               // terlalu generik, rules jadi campur aduk
```

```php
class ThesisRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gate/policy dihandle di route/controller
    }

    public function rules(): array
    {
        return [
            'mahasiswa_id'  => 'required|exists:ref_mahasiswa,id',
            'thesis_type'   => 'required|in:penelitian,reguler',
            'title'         => 'required|string|max:500',
            'abstract'      => 'nullable|string',
            'supervisor_1'  => 'required|exists:ref_dosen,id',
            'supervisor_2'  => 'nullable|exists:ref_dosen,id|different:supervisor_1',
        ];
    }
}
```

### Laravel — Model Scopes

```php
// Reusable query constraints
public function scopeAktif($query)
{
    return $query->where('status_mhs', 'aktif');
}

public function scopeByStatus($query, string $status)
{
    return $query->where('status', $status);
}

public function scopeBelumPunyaTesis($query)
{
    return $query->whereDoesntHave('theses', fn($q) =>
        $q->whereNotIn('status', ['completed', 'rejected'])
    );
}
```

### Laravel — Redirect dengan Flash Message

Konsisten gunakan format array dengan `title` dan `description`:

```php
return redirect()->route('elektro.admin.thesis.index')
    ->with('success', [
        'title'       => 'Tesis Disetujui',
        'description' => "Tesis {$thesis->mahasiswa->nama_mhs} berhasil disetujui.",
    ]);

return redirect()->back()
    ->with('error', [
        'title'       => 'Gagal',
        'description' => 'Tesis tidak dapat disetujui pada status saat ini.',
    ]);
```

### Vue — Struktur Page

```vue
<script setup lang="ts">
// 1. Imports
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

// 2. Types & Props
interface Props { thesis: Thesis; dosens: Dosen[] }
const props = defineProps<Props>()

// 3. Breadcrumbs
const breadcrumbs = [
    { title: 'Thesis', href: route('elektro.admin.thesis.index') },
    { title: 'Edit', href: '#' },
]

// 4. Form / State
const form = useForm({
    title:       props.thesis.title,
    thesis_type: props.thesis.thesis_type,
})

// 5. Methods
const submit = () => form.put(route('elektro.admin.thesis.update', props.thesis.id))
</script>

<template>
    <Head title="Edit Tesis" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- content -->
        </div>
    </AppLayout>
</template>
```

### Vue — Naming Routes di Template

```vue
<!-- BAIK -->
<Link :href="route('elektro.admin.thesis.index')">Kembali</Link>
<Button @click="router.visit(route('elektro.admin.thesis.create'))">Tambah</Button>

<!-- BURUK — jangan hardcode URL -->
<Link href="/elektro/admin/thesis">Kembali</Link>
```

---

## 10. Menghindari Overengineering

### Test Sederhana

> "Apakah kompleksitas ini menyelesaikan masalah yang *sekarang* ada?"

Jika jawabannya tidak — jangan tambahkan.

### Tabel Keputusan

| Pattern | Gunakan? | Alasan |
|---------|----------|--------|
| Repository pattern | Tidak | Eloquent sudah cukup sebagai data layer |
| Service layer | Hanya jika diperlukan | Buat hanya ketika logika sama dipakai 2+ controller |
| Events/Listeners | Hanya untuk notifikasi | Jangan fire events "untuk masa depan" |
| DTO / API Resource | Tidak perlu | Pattern `toListItem()` + typed props sudah cukup |
| Strategy pattern (institusi) | Belum | Pemisahan namespace sudah cukup sampai kebutuhan PWK jelas |
| Abstract base controller | Hanya jika 80%+ kode sama | Jangan preemptif |
| Global state (Pinia) | Tidak perlu | Inertia shared props sudah handle auth/user state |

### Hal Konkret yang Harus Segera Diperbaiki

1. **`UserManagementController@store` belum menyimpan data** — `$validatedData` dikumpulkan tapi tidak ada `User::create()`
2. **Tombol Import dobel di `user/Index.vue`** — ada tombol non-fungsional (baris ~167) dan AlertDialog yang benar. Hapus yang pertama.
3. **Guard `study_program_id` tidak konsisten** — `byStudyProgram` scope hanya di `index()`, tidak di `show()`, `edit()`, `destroy()`. Admin dari satu institusi bisa akses user institusi lain.
4. **Standardisasi casing path Inertia** — gunakan lowercase konsisten seperti yang sudah berjalan sekarang (`'shared/admin/user/Index'`) atau PascalCase, pilih satu.

---

## Urutan Development yang Disarankan

```
[x] Auth (login, change password, profile)
[x] User Management (index, show, edit, delete, reset password)
[ ] User Management (store — selesaikan)
[ ] Mahasiswa Management (index, show) — route stub sudah ada
[ ] Dosen Management
[ ] Thesis Registration (admin mendaftarkan tesis mahasiswa)
[ ] Thesis Approval (admin approve + assign supervisor)
[ ] Logbook (mahasiswa submit, dosen konfirmasi)
[ ] Seminar (admin jadwalkan, input nilai)
[ ] Defense/Pendadaran (admin jadwalkan, input nilai)
[ ] Laporan & Export
```

---

## 11. Current State Audit

> Terakhir diperbarui: 2026-04-22

### Yang Sudah Dibangun

| Modul | Status | Catatan |
|-------|--------|---------|
| Auth — Login | ✅ Done | `LoginController`, halaman `auth/Login.vue` |
| Auth — Change Password (first login) | ✅ Done | `FirstLoginController`, middleware `firstlogin` |
| Auth — Profile (edit + foto + password) | ✅ Done | `ProfileController` |
| Auth — Logout | ✅ Done | |
| User Management — Index | ✅ Done | Filter, sort, pagination, filter by role |
| User Management — Show | ✅ Done | |
| User Management — Edit + Update | ✅ Done | Role sync via `user_roles` |
| User Management — Delete | ✅ Done | Soft delete, tidak bisa hapus diri sendiri |
| User Management — Reset Password | ✅ Done | Reset ke `nomor_induk` |
| User Management — Create (UI) | ⚠️ Partial | Form ada, tapi `store()` masih kosong — tidak menyimpan data |
| Mahasiswa Management — Index | ⚠️ Partial | Controller + UI ada, tapi ada bug field `status` |
| Helpers — Filterable + Sortable | ✅ Done | Trait dipakai di `User`, `Mahasiswa` |
| Composables — `useTable` | ✅ Done | Debounce, sort, filter, pagination |
| Composables — `useFlashSonner`, `useAuth`, dll | ✅ Done | |
| UI Components — shadcn-vue library | ✅ Done | Tidak perlu diubah |
| UI Components — DataTable, FilterTable, FormInput, FormSelect, ConfirmDialog | ✅ Done | |
| Dosen Management | ❌ Todo | Route stub ada, controller belum |
| Ruang / Jabatan / MK Management | ❌ Todo | Route stub ada, controller belum |
| Semua fitur Tesis (Elektro & PWK) | ❌ Todo | |
| Middleware `StudyProgramMiddleware` | ❌ Todo | Dipakai di `routes/elektro.php` tapi belum ada di `app/Http/Middleware/` |

### Bug / Inkonsistensi yang Harus Diperbaiki

| # | Bug | Lokasi | Dampak |
|---|-----|--------|--------|
| 1 | `store()` kosong — data tidak disimpan | `UserManagementController@store` | User baru tidak bisa dibuat |
| 2 | Guard `study_program_id` hilang di show/edit/destroy | `UserManagementController` | Admin institusi A bisa akses user institusi B |
| 3 | `toSearchResult()` akses `$this->status` padahal field-nya `status_mhs` | `Mahasiswa@toSearchResult()` (Elektro & PWK) | Mahasiswa index crash / return null |
| 4 | `StudyProgramMiddleware` belum ada | `app/Http/Middleware/` | Route `/elektro/*` dan `/pwk/*` tidak akan berfungsi |

---

## 12. Pemetaan Legacy → Baru (Elektro)

Sumber: `portalelektro` — fitur yang diambil: **Tugas Akhir, Seminar Hasil, Sidang Pendadaran**  
Target: `portalmagister` Elektro — nama baru: **Tesis, Seminar Hasil, Sidang**

### Tabel Utama

| Legacy Table | Legacy Field | → Tabel Baru | → Field Baru | Keterangan |
|---|---|---|---|---|
| `ta` | `mahasiswa_id` | `theses` | `mahasiswa_id` | FK → `ref_mahasiswa` |
| `ta` | `judul_ta` | `theses` | `title` | |
| `ta` | `abstrak` | `theses` | `abstract` | |
| `ta` | `status_ta` (PENDING/SETUJU/TOLAK) | `theses` | `status` (enum) | Lihat lifecycle di bawah |
| `ta` | `tgl_pengajuan` | `theses` | `created_at` | |
| `ta` | `tgl_setuju` | `theses` | `started_at` | |
| `ta` | `sks`, `ipk` | `ref_mahasiswa` | `sks`, `ipk` | Sudah ada di mahasiswa |
| `ta` | `peminatan_id` | — | **DIHAPUS** | Tidak ada peminatan/KBK di S2 |
| `ta_pembimbing` | `pembimbing` (FK dosen) | `thesis_supervisors` | `dosen_id` | |
| `ta_pembimbing` | `pem` (1=utama, 2=co) | `thesis_supervisors` | `position` ('utama'/'co') | |
| `ta_logbook` | `mahasiswa_id` | `thesis_logbooks` | `thesis_id` | FK diubah ke tesis, bukan mahasiswa |
| `ta_logbook` | `isi_logbook` | `thesis_logbooks` | `description` | |
| `ta_logbook` | `tgl_bimbingan` | `thesis_logbooks` | `log_date` | |
| `ta_logbook` | `status_logbook1` (konfirmasi pem1) | `thesis_logbooks` | `supervisor_signed_at` | Digabung jadi satu timestamp |
| `ta_seminar` | `ta_id` | `thesis_seminars` | `thesis_id` | |
| `ta_seminar` | `tempat` | `thesis_seminars` | `room_id` | FK → `ref_ruang` |
| `ta_seminar` | `tanggal`, `jam_mulai` | `thesis_seminars` | `scheduled_at` | |
| `ta_seminar` | `status_seminar` | `thesis_seminars` | `status` | |
| `ta_seminar` | `draft_semhas` | `thesis_seminars` | `file_presentation` | |
| `ta_penguji` | `penguji_semhas` (FK dosen) | `thesis_seminar_examiners` | `dosen_id` (role='penguji') | |
| `ta_nilaisemhas_pembimbing` | nilai | `thesis_seminar_examiners` | `grade` (role='pembimbing') | Digabung ke satu tabel examiner |
| `ta_nilaisemhas_penguji` | nilai | `thesis_seminar_examiners` | `grade` (role='penguji') | |
| `ta_pendadaran` | `ta_id` | `thesis_defenses` | `thesis_id` | |
| `ta_pendadaran` | `tempat` | `thesis_defenses` | `room_id` | FK → `ref_ruang` |
| `ta_pendadaran` | `tanggal` | `thesis_defenses` | `scheduled_at` | |
| `ta_pendadaran` | `status_pendadaran` | `thesis_defenses` | `status` | |
| `ta_penguji` | `penguji_pendadaran` (FK dosen) | `thesis_defense_examiners` | `dosen_id` | Tabel examiner terpisah untuk sidang |
| `ta_nilaipendadaran_pembimbing` | nilai | `thesis_defense_examiners` | `grade` (role='pembimbing') | |
| `ta_nilaipendadaran_penguji` | nilai | `thesis_defense_examiners` | `grade` (role='penguji') | |

### Status Lifecycle

```
Legacy:   PENDING ──→ SETUJU / TOLAK

Baru:     draft ──→ registered ──→ supervision ──→ seminar ──→ defense ──→ completed
                                                                       ↘
                                                              rejected (dari status manapun)
```

### Yang Dihapus dari Legacy (Out of Scope)

| Legacy | Alasan Dihapus |
|--------|---------------|
| KP (Kerja Praktek) | Tidak ada di S2 |
| Capstone | Tidak ada di S2 |
| `peminatan` / KBK | Tidak ada konsentrasi di S2 |
| Role `koordinatorsel`, `koordinatormeka`, `koordinatorict` | Tidak relevan S2 |
| `log_judul_ta`, `log_pembimbing_ta`, `log_perpanjangan_ta` | Perubahan judul/pembimbing/perpanjangan — TBD apakah perlu di S2 |
| Analisis ML | Bukan scope |
| Biodata alumni / exit survey | Bukan scope untuk sekarang |
| Bebas lab | Bukan scope untuk sekarang |
| Wisuda management | Bukan scope untuk sekarang |

---

## 13. Spesifikasi Fitur Elektro

### 13.1 Pendaftaran Tesis

**Flow:**
```
Admin buat record tesis untuk mahasiswa
    → assign pembimbing utama (wajib) + co (opsional)
    → status: draft → registered
Admin aktifkan bimbingan
    → status: registered → supervision
```

**Actor:** Admin

**Routes:**
```
GET    /elektro/admin/tesis              → index
GET    /elektro/admin/tesis/create       → create
POST   /elektro/admin/tesis              → store
GET    /elektro/admin/tesis/{id}         → show
GET    /elektro/admin/tesis/{id}/edit    → edit
PUT    /elektro/admin/tesis/{id}         → update
DELETE /elektro/admin/tesis/{id}         → destroy
```

**Controller:** `Elektro\Admin\TesisController`  
**Model:** `Elektro\Tesis` — connection `elektro`, table `theses`  
**Form Request:** `ThesisRegistrationRequest`, `ThesisUpdateRequest`

**DB:** Tabel `theses` (lihat Section 5 untuk skema lengkap)

---

### 13.2 Logbook (Bimbingan)

**Flow:**
```
Mahasiswa submit entri logbook (tanggal, deskripsi kegiatan)
    → Dosen pembimbing lihat dan konfirmasi / tolak
    → Status tesis tetap 'supervision' selama proses berlangsung
```

**Actor:** Mahasiswa (create), Dosen (confirm)

**Routes (Mahasiswa):**
```
GET    /elektro/mahasiswa/tesis              → my thesis (overview)
GET    /elektro/mahasiswa/logbook            → list logbook saya
POST   /elektro/mahasiswa/logbook            → submit entri baru
DELETE /elektro/mahasiswa/logbook/{id}       → hapus entri (jika belum dikonfirmasi)
```

**Routes (Dosen):**
```
GET    /elektro/dosen/bimbingan                          → list mahasiswa bimbingan
GET    /elektro/dosen/bimbingan/{tesisId}/logbook        → logbook per mahasiswa
PATCH  /elektro/dosen/bimbingan/{tesisId}/logbook/{id}  → konfirmasi / tolak
```

**Controllers:** `Elektro\Mahasiswa\LogbookController`, `Elektro\Dosen\LogbookController`  
**Model:** `Elektro\ThesisLogbook` — connection `elektro`, table `thesis_logbooks`

---

### 13.3 Seminar Hasil

**Flow:**
```
Admin jadwalkan seminar (tanggal, ruang)
    → Admin assign penguji: 1 ketua, N penguji, pembimbing(s)
    → Masing-masing dosen input nilai (grade per examiner)
    → Admin finalisasi → hitung nilai akhir
    → Status seminar: scheduled → done
    → Status tesis: seminar → defense (jika lulus)
```

**Actor:** Admin (jadwal + finalisasi), Dosen (input nilai)

**Routes (Admin):**
```
GET    /elektro/admin/seminar                → index seminar terjadwal
GET    /elektro/admin/seminar/create         → form jadwalkan (pilih tesis, tanggal, ruang)
POST   /elektro/admin/seminar                → store jadwal + assign examiner
GET    /elektro/admin/seminar/{id}           → show detail + rekap nilai
PATCH  /elektro/admin/seminar/{id}/finalize  → finalisasi nilai akhir
```

**Routes (Dosen):**
```
GET    /elektro/dosen/seminar               → list seminar yang saya terlibat
GET    /elektro/dosen/seminar/{id}          → detail + form input nilai
POST   /elektro/dosen/seminar/{id}/nilai    → submit nilai
```

**Controllers:** `Elektro\Admin\SeminarController`, `Elektro\Dosen\SeminarController`  
**Models:** `Elektro\ThesisSeminar`, `Elektro\ThesisSeminarExaminer`

**Catatan desain:**
- Tabel `thesis_seminar_examiners`: `role` ENUM('ketua','penguji','pembimbing'), `grade` DECIMAL(4,2)
- Formula nilai akhir (bobot pembimbing vs penguji) — **TBD, konfirmasi dengan pihak prodi sebelum implementasi**
- Seminar Proposal tidak ada di S2 Elektro — `type` di `thesis_seminars` = `'hasil'` saja

---

### 13.4 Sidang Pendadaran

**Flow identik dengan Seminar Hasil**, perbedaannya:
- Tabel: `thesis_defenses` (bukan `thesis_seminars`)
- Tabel examiner: `thesis_defense_examiners`
- Tesis harus berstatus `seminar` (seminar sudah selesai) sebelum bisa dijadwalkan sidang
- Setelah selesai: status tesis → `completed`

**Routes (Admin):**
```
GET    /elektro/admin/sidang                → index
GET    /elektro/admin/sidang/create         → form jadwalkan
POST   /elektro/admin/sidang                → store
GET    /elektro/admin/sidang/{id}           → show + rekap nilai
PATCH  /elektro/admin/sidang/{id}/finalize  → finalisasi
```

**Routes (Dosen):**
```
GET    /elektro/dosen/sidang               → list sidang yang saya terlibat
GET    /elektro/dosen/sidang/{id}          → detail + form input nilai
POST   /elektro/dosen/sidang/{id}/nilai    → submit nilai
```

**Controllers:** `Elektro\Admin\SidangController`, `Elektro\Dosen\SidangController`  
**Models:** `Elektro\ThesisDefense`, `Elektro\ThesisDefenseExaminer`

---

### 13.5 View Mahasiswa (My Thesis)

Mahasiswa hanya bisa melihat satu tesis aktif miliknya. Satu halaman overview:

```
/elektro/mahasiswa/tesis    → overview status tesis saya
                               └─ tab/section Logbook
                               └─ tab/section Seminar Hasil (jika sudah terjadwal)
                               └─ tab/section Sidang (jika sudah terjadwal)
```

---

## 14. Catatan PWK (Build from Scratch)

PWK **tidak** memiliki legacy system. Sistem dibangun dari nol dengan flow yang berbeda dari Elektro.

### Yang Diketahui Sekarang

| Aspek | Keterangan |
|-------|-----------|
| DB Connection | `pwk` (`dbmpwk`) |
| Namespace Model | `App\Models\PWK\*` |
| Namespace Controller | `App\Http\Controllers\PWK\*` |
| Halaman | `resources/js/pages/pwk/*` |
| DB Shared | `User`, `Role`, `StudyProgram` tetap di `main` DB |
| Referensi (`ref_mahasiswa`, `ref_dosen`, dll) | Struktur sama dengan Elektro, migrasi `standard/` dijalankan ke koneksi `pwk` |

### Yang Belum Diketahui

- Flow approval tesis PWK (apakah multi-tahap?)
- Apakah ada seminar proposal di PWK?
- Jumlah dan role penguji di sidang PWK
- Formula penilaian PWK

**Keputusan:** PWK akan dispesifikasikan dalam dokumen terpisah setelah sistem Elektro stabil dan kebutuhan PWK dikonfirmasi dengan pihak prodi.

---

## 15. Roadmap Development (Updated)

### Prioritas Segera (Bug Fix)

```
[ ] Fix: UserManagementController@store — tambahkan User::create() + assign role + study_program_id
[ ] Fix: Guard study_program_id di show(), edit(), update(), destroy()
[ ] Fix: Mahasiswa@toSearchResult() — ganti $this->status → $this->status_mhs
[ ] Fix: Buat StudyProgramMiddleware (dipakai di routes/elektro.php & pwk.php)
```

### Phase 1 — Referensi & Master Data (Shared Admin)

```
[ ] Dosen Management (index, create, store, show, edit, update, delete)
[ ] Ruang Management (index, create, store, edit, update, delete)
[ ] Jabatan Management
[ ] Mata Kuliah Management
```

### Phase 2 — Tesis Elektro: Pendaftaran & Bimbingan

```
[ ] Migration: theses, thesis_supervisors, thesis_logbooks
[ ] Model: Elektro\Tesis, Elektro\ThesisSupervisor, Elektro\ThesisLogbook
[ ] Controller + Routes: Elektro\Admin\TesisController (CRUD)
[ ] Controller + Routes: Elektro\Mahasiswa\LogbookController
[ ] Controller + Routes: Elektro\Dosen\LogbookController (konfirmasi)
[ ] Pages: elektro/admin/tesis/*, elektro/mahasiswa/logbook/*, elektro/dosen/bimbingan/*
```

### Phase 3 — Tesis Elektro: Seminar Hasil

```
[ ] Migration: thesis_seminars, thesis_seminar_examiners
[ ] Model: Elektro\ThesisSeminar, Elektro\ThesisSeminarExaminer
[ ] Controller + Routes: Elektro\Admin\SeminarController
[ ] Controller + Routes: Elektro\Dosen\SeminarController (nilai)
[ ] Pages: elektro/admin/seminar/*, elektro/dosen/seminar/*
[ ] Konfirmasi formula nilai akhir dengan prodi sebelum implementasi
```

### Phase 4 — Tesis Elektro: Sidang Pendadaran

```
[ ] Migration: thesis_defenses, thesis_defense_examiners
[ ] Model: Elektro\ThesisDefense, Elektro\ThesisDefenseExaminer
[ ] Controller + Routes: Elektro\Admin\SidangController
[ ] Controller + Routes: Elektro\Dosen\SidangController (nilai)
[ ] Pages: elektro/admin/sidang/*, elektro/dosen/sidang/*
```

### Phase 5 — PWK (TBD)

```
[ ] Buat spec terpisah setelah kebutuhan PWK dikonfirmasi
[ ] Jalankan migrasi standard/ ke koneksi pwk
[ ] Implementasi sesuai spec
```

### Phase 6 — Laporan & Export (Opsional, Nanti)

```
[ ] Export PDF undangan seminar / sidang
[ ] Export rekap nilai
[ ] Export data tesis
```
