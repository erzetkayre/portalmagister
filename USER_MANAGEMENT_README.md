# User Management System Documentation

## Overview
This is a comprehensive user management system built with Laravel (backend) and Vue.js with Inertia.js (frontend). The system provides functionality to list, create, edit, and delete users with role-based access control.

## 📁 File Structure
```
resources/js/pages/admin/managements/
├── users.vue              # Main user listing page
└── users/
    └── Create.vue         # Create new user form
```

## 🚀 Features
- **User Listing**: Paginated table with search and filters
- **User Creation**: Elegant form with validation
- **Role Management**: Admin, Dosen, Mahasiswa roles
- **Search & Filter**: By name, email, role, and status
- **Responsive Design**: Mobile-friendly interface
- **Real-time Validation**: Form error handling

---

## 📄 Main User Listing Page (`users.vue`)

### 🎯 Component Structure

#### **Script Section**
```typescript
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { BookUser, GraduationCap, UsersRound, Plus, Upload, Search, Filter, Eye, KeyRound, Edit, Trash2, MoreHorizontal } from 'lucide-vue-next';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Pagination, PaginationContent, PaginationItem, PaginationNext, PaginationPrevious, PaginationEllipsis } from '@/components/ui/pagination';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { ref, watch } from 'vue';
```

**Explanation:**
- Imports all necessary UI components from the component library
- Uses Inertia.js for seamless navigation and form handling
- Imports Vue 3 Composition API functions (`ref`, `watch`)
- Lucide Vue icons for visual elements

#### **TypeScript Interfaces**
```typescript
interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    status: 'active' | 'inactive';
    created_at: string;
}

interface PaginatedUsers {
    data: User[];
    current_page: number;
    from: number;
    to: number;
    total: number;
    last_page: number;
    per_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

interface Props {
    users: PaginatedUsers;
    roles: string[];
    filters: {
        search?: string;
        role?: string;
        status?: string;
    };
}
```

**Explanation:**
- **User Interface**: Defines the structure of a user object
- **PaginatedUsers**: Laravel pagination structure
- **Props Interface**: Defines what data the component receives from the backend

#### **Reactive State Management**
```typescript
const props = defineProps<Props>();

const isDialogOpen = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

// Filter states
const searchTerm = ref(props.filters.search || '');
const selectedRole = ref(props.filters.role || '');
const selectedStatus = ref(props.filters.status || '');

const form = useForm({
    file: null as File | null,
});
```

**Explanation:**
- **Props**: Receives data from Laravel backend
- **Reactive References**: For managing component state
- **Form**: Inertia.js form helper for file uploads

#### **Core Functions**

##### **Filter Functions**
```typescript
const applyFilters = () => {
    const filters: any = {};
    if (searchTerm.value) filters.search = searchTerm.value;
    if (selectedRole.value) filters.role = selectedRole.value;
    if (selectedStatus.value) filters.status = selectedStatus.value;

    router.get('/admin/users', filters, {
        preserveState: true,
        preserveScroll: true,
    });
};
```

**Explanation:**
- Builds filter object from current form values
- Uses Inertia router to navigate with filters as query parameters
- `preserveState` and `preserveScroll` maintain user experience during filtering

##### **Action Functions**
```typescript
const showUser = (userId: number) => {
    router.get(`/admin/users/${userId}`);
};

const editUser = (userId: number) => {
    router.get(`/admin/users/${userId}/edit`);
};

const deleteUser = (userId: number) => {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        router.delete(`/admin/users/${userId}`, {
            onSuccess: () => {
                alert('User deleted successfully!');
            },
        });
    }
};
```

**Explanation:**
- **showUser**: Navigate to user detail page
- **editUser**: Navigate to user edit form
- **deleteUser**: Delete user with confirmation dialog

### 🎨 Template Structure

#### **Statistics Cards**
```vue
<div class="grid auto-rows-min gap-4 md:grid-cols-3">
    <Card class="gap-2">
        <CardHeader class="flex flex-row justify-between items-center">
            <CardTitle class="text-lg font-medium">Total User</CardTitle>
            <BookUser class="h-6 w-6 text-destructive" />
        </CardHeader>
        <CardContent>
            <div class="text-3xl font-bold">2,350</div>
            <div class="flex item-center pt-2 justify-between">
                <p class="text-sm">Jumlah user terdaftar</p>
                <Badge>1,890 aktif</Badge>
            </div>
        </CardContent>
    </Card>
    <!-- More cards... -->
</div>
```

**Explanation:**
- Grid layout for responsive design
- Each card shows different user statistics
- Icons and badges for visual appeal

#### **Action Buttons**
```vue
<div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold">Daftar Users</h3>
    <Button @click="router.get('/admin/users/create')">
        <Plus class="h-4 w-4 mr-2" />
        Tambah User
    </Button>
</div>
```

**Explanation:**
- **Tambah User Button**: Redirects to create form using Inertia router
- Clean button design with icon

#### **Filter Section**
```vue
<div class="flex items-center space-x-4 mb-4">
    <div class="flex items-center space-x-2">
        <Search class="h-4 w-4 text-gray-500" />
        <Input v-model="searchTerm" placeholder="Cari nama atau email..." class="w-64" />
    </div>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="outline" class="border-dashed">
                <Filter class="h-4 w-4 mr-2" />
                Role
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent>
            <DropdownMenuItem @click="selectedRole = ''; applyFilters()">
                All Roles
            </DropdownMenuItem>
            <DropdownMenuItem v-for="role in roles" :key="role" @click="selectedRole = role; applyFilters()">
                {{ role }}
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</div>
```

**Explanation:**
- **Search Input**: Real-time search with debouncing
- **Dropdown Filters**: For role and status filtering
- **Clear Filters**: Reset all filters

#### **Data Table**
```vue
<Table>
    <TableHeader>
        <TableRow>
            <TableHead>No</TableHead>
            <TableHead>Name</TableHead>
            <TableHead>Email</TableHead>
            <TableHead>Role</TableHead>
            <TableHead>Status</TableHead>
            <TableHead>Created At</TableHead>
            <TableHead>Actions</TableHead>
        </TableRow>
    </TableHeader>
    <TableBody>
        <TableRow v-for="(user, index) in props.users.data" :key="user.id">
            <TableCell>{{ (props.users.current_page - 1) * props.users.per_page + index + 1 }}</TableCell>
            <TableCell class="font-medium">{{ user.name }}</TableCell>
            <TableCell>{{ user.email }}</TableCell>
            <TableCell>
                <Badge variant="outline">{{ user.role }}</Badge>
            </TableCell>
            <TableCell>
                <Badge :variant="user.status === 'active' ? 'default' : 'destructive'">
                    {{ user.status }}
                </Badge>
            </TableCell>
            <TableCell>{{ new Date(user.created_at).toLocaleDateString('id-ID') }}</TableCell>
            <TableCell>
                <div class="flex items-center gap-2">
                    <Button size="sm" variant="ghost" @click="showUser(user.id)">
                        <Eye class="h-4 w-4" />
                    </Button>
                    <Button size="sm" variant="ghost" @click="editUser(user.id)">
                        <Edit class="h-4 w-4" />
                    </Button>
                    <Button size="sm" variant="ghost" @click="deleteUser(user.id)">
                        <Trash2 class="h-4 w-4" />
                    </Button>
                </div>
            </TableCell>
        </TableRow>
    </TableBody>
</Table>
```

**Explanation:**
- **Row Numbering**: Calculates correct row numbers across pages
- **Status Badges**: Color-coded based on user status
- **Action Buttons**: View, Edit, Delete operations
- **Date Formatting**: Indonesian locale formatting

#### **Pagination**
```vue
<Pagination v-if="props.users.last_page > 1" v-slot="{ page }" :items-per-page="props.users.per_page" :total="props.users.total" :default-page="props.users.current_page">
    <PaginationContent v-slot="{ items }">
        <PaginationPrevious @click="goToPage(props.users.current_page - 1)" />
        <template v-for="(item, index) in items" :key="index">
            <PaginationItem v-if="item.type === 'page'" :value="item.value" :is-active="item.value === page" @click="goToPage(item.value)">
                {{ item.value }}
            </PaginationItem>
        </template>
        <PaginationNext @click="goToPage(props.users.current_page + 1)" />
    </PaginationContent>
</Pagination>
```

**Explanation:**
- Only shows pagination if there are multiple pages
- Handles previous/next navigation
- Maintains filters during pagination

---

## 📝 Create User Form (`Create.vue`)

### 🎯 Component Structure

#### **Script Section**
```typescript
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { User, Mail, Lock, UserCheck, ArrowLeft, Save } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
});

const roles = [
    { value: 'admin', label: 'Administrator', description: 'Full access to all features' },
    { value: 'dosen', label: 'Dosen', description: 'Teacher access with student management' },
    { value: 'mahasiswa', label: 'Mahasiswa', description: 'Student access with limited features' },
];

const submit = () => {
    form.post('/admin/users', {
        onSuccess: () => {
            router.get('/admin/users');
        },
    });
};
```

**Explanation:**
- **Form Object**: Inertia.js form helper with all form fields
- **Roles Array**: Defines available user roles with descriptions
- **Submit Function**: Posts form data to backend and redirects on success

### 🎨 Template Structure

#### **Header Section**
```vue
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold tracking-tight">Tambah User Baru</h1>
        <p class="text-muted-foreground mt-1">Buat akun pengguna baru untuk sistem portal</p>
    </div>
    <Button variant="outline" @click="router.get('/admin/users')" class="gap-2">
        <ArrowLeft class="h-4 w-4" />
        Kembali
    </Button>
</div>
```

**Explanation:**
- Clean header with title and description
- Back button for navigation

#### **Form Fields**

##### **Name Field**
```vue
<div class="space-y-2">
    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
        Nama Lengkap
    </label>
    <div class="relative">
        <User class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
        <Input 
            v-model="form.name" 
            placeholder="Masukkan nama lengkap" 
            class="pl-10"
            :class="{ 'border-destructive': form.errors.name }"
            required 
        />
    </div>
    <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
</div>
```

**Explanation:**
- **Icon Integration**: User icon positioned inside input field
- **Error Handling**: Dynamic styling and error message display
- **Accessibility**: Proper labeling and required attributes

##### **Role Selection**
```vue
<div class="space-y-3">
    <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">
        Pilih Role
    </label>
    <div class="grid gap-3">
        <div 
            v-for="role in roles" 
            :key="role.value"
            class="flex items-center space-x-3 rounded-lg border p-4 cursor-pointer transition-all hover:bg-muted/50"
            :class="{ 'border-primary bg-primary/5': form.role === role.value }"
            @click="form.role = role.value"
        >
            <input 
                :id="role.value"
                v-model="form.role" 
                :value="role.value"
                type="radio" 
                class="h-4 w-4 text-primary focus:ring-primary"
                required
            />
            <div class="flex-1 space-y-1">
                <div class="flex items-center gap-2">
                    <UserCheck class="h-4 w-4 text-primary" />
                    <label :for="role.value" class="text-sm font-medium cursor-pointer">
                        {{ role.label }}
                    </label>
                    <Badge variant="outline" class="text-xs">
                        {{ role.value }}
                    </Badge>
                </div>
                <p class="text-sm text-muted-foreground">{{ role.description }}</p>
            </div>
        </div>
    </div>
</div>
```

**Explanation:**
- **Interactive Cards**: Clickable role selection with visual feedback
- **Radio Buttons**: Proper form control with labels
- **Visual Indicators**: Icons, badges, and descriptions for each role
- **Hover Effects**: Enhanced user experience

#### **Action Buttons**
```vue
<div class="flex items-center justify-end space-x-3 pt-6 border-t">
    <Button 
        type="button" 
        variant="outline" 
        @click="router.get('/admin/users')"
        :disabled="form.processing"
    >
        Batal
    </Button>
    <Button 
        type="submit" 
        :disabled="form.processing"
        class="gap-2"
    >
        <Save class="h-4 w-4" />
        {{ form.processing ? 'Menyimpan...' : 'Simpan User' }}
    </Button>
</div>
```

**Explanation:**
- **Loading States**: Disabled buttons during form submission
- **Dynamic Text**: Shows loading state in submit button
- **Proper Spacing**: Border separator and consistent spacing

#### **Security Tips Card**
```vue
<Card class="max-w-2xl">
    <CardContent class="pt-6">
        <div class="flex items-start gap-3">
            <div class="rounded-full bg-blue-100 p-2">
                <UserCheck class="h-4 w-4 text-blue-600" />
            </div>
            <div class="space-y-1">
                <p class="text-sm font-medium">Tips Keamanan</p>
                <ul class="text-sm text-muted-foreground space-y-1">
                    <li>• Gunakan password yang kuat dengan minimal 8 karakter</li>
                    <li>• Pilih role sesuai dengan kebutuhan akses pengguna</li>
                    <li>• Pastikan email yang digunakan valid dan dapat diakses</li>
                </ul>
            </div>
        </div>
    </CardContent>
</Card>
```

**Explanation:**
- **User Guidance**: Helpful tips for form completion
- **Visual Design**: Icon and color-coded information
- **Best Practices**: Security recommendations for users

---

## 🔧 Backend Integration

### Laravel Controller Structure
```php
<?php

namespace App\Http\Controllers\Admin\Managements;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        // Apply role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $users = $query->paginate(10);
        
        return Inertia::render('admin/managements/users', [
            'users' => $users,
            'roles' => ['admin', 'dosen', 'mahasiswa'],
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }
    
    public function create()
    {
        return Inertia::render('admin/managements/users/Create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,dosen,mahasiswa',
        ]);
        
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => 'active',
        ]);
        
        return redirect()->route('admin.users.index')
                        ->with('success', 'User berhasil ditambahkan');
    }
}
```

### Route Configuration
```php
// routes/web.php
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
});
```

---

## 🎯 Key Features Explained

### 1. **Inertia.js Integration**
- Seamless navigation without page reloads
- Form handling with built-in validation
- State preservation during navigation

### 2. **Real-time Search & Filtering**
- Debounced search input for performance
- Multiple filter options (role, status)
- URL-based filter persistence

### 3. **Responsive Design**
- Mobile-friendly layout
- Adaptive grid system
- Touch-friendly interactions

### 4. **Form Validation**
- Client-side validation feedback
- Server-side validation integration
- Real-time error display

### 5. **User Experience**
- Loading states during operations
- Confirmation dialogs for destructive actions
- Intuitive navigation patterns

---

## 🚀 Usage Instructions

### 1. **Viewing Users**
- Navigate to `/admin/users`
- Use search bar to find specific users
- Apply filters using dropdown menus
- Navigate between pages using pagination

### 2. **Creating New Users**
- Click "Tambah User" button
- Fill in all required fields
- Select appropriate role
- Submit form to create user

### 3. **Managing Users**
- Use action buttons in table rows
- View user details with eye icon
- Edit users with edit icon
- Delete users with trash icon (confirmation required)

---

## 🔐 Security Features

### 1. **Password Security**
- Minimum 8 character requirement
- Password confirmation validation
- Automatic password hashing

### 2. **Role-Based Access**
- Three distinct roles: Admin, Dosen, Mahasiswa
- Role-specific permissions
- Secure role assignment

### 3. **Data Validation**
- Server-side validation
- Email uniqueness checking
- Input sanitization

---

## 📱 Responsive Design

### Desktop (≥768px)
- 3-column statistics grid
- Full-width table
- Side-by-side form layout

### Mobile (<768px)
- Single-column layout
- Stacked form fields
- Collapsible navigation

---

## 🎨 Design System

### Colors
- Primary: Brand color for interactive elements
- Destructive: Red for delete actions and errors
- Muted: Gray for secondary text
- Background: Clean white/dark mode support

### Typography
- Headings: Bold, clear hierarchy
- Body text: Readable font sizes
- Labels: Consistent styling

### Spacing
- Consistent gap measurements
- Proper padding and margins
- Visual rhythm throughout

---

## 🔧 Technical Requirements

### Frontend Dependencies
- Vue 3 with Composition API
- Inertia.js for SPA-like experience
- Tailwind CSS for styling
- Lucide Vue for icons

### Backend Requirements
- Laravel 10+
- PHP 8.1+
- MySQL/PostgreSQL database
- Inertia.js server-side adapter

---

## 📈 Performance Optimizations

### Frontend
- Lazy loading for large datasets
- Debounced search input
- Optimized re-renders

### Backend
- Efficient database queries
- Pagination for large datasets
- Indexed database columns

---

## 🐛 Error Handling

### Form Errors
- Real-time validation feedback
- Clear error messages
- Visual error indicators

### Network Errors
- Graceful error handling
- User-friendly error messages
- Retry mechanisms

---

## 🚀 Future Enhancements

### Potential Features
- Bulk user operations
- Advanced filtering options
- User activity logs
- Email verification system
- Profile picture uploads
- Export functionality

### Performance Improvements
- Virtual scrolling for large datasets
- Advanced caching strategies
- Real-time updates with WebSockets

---

This documentation provides a comprehensive overview of the user management system, explaining both the frontend Vue components and backend Laravel integration. The system is designed to be maintainable, scalable, and user-friendly.