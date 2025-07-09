<?php

namespace App\Http\Controllers\Admin\Managements;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RolesController extends Controller
{
    public function index() {
        return Inertia::render('admin/managements/Roles');
    }
}
