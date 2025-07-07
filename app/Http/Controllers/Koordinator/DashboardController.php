<?php

namespace App\Http\Controllers\Koordinator;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index() {
        return Inertia::render('koordinator/Dashboard');
    }

}
