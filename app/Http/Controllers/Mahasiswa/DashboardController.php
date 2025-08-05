<?php

namespace App\Http\Controllers\Mahasiswa;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index() {
        $mahasiswaId = auth()->id(); // pastikan user sudah login
        $statusPratesis = 'Not Started';
        $statusUjiantesis = 'Not Started';

        return Inertia::render('mahasiswa/Dashboard',[
            'status_pratesis' => $statusPratesis,
            'status_ujiantesis' => $statusUjiantesis,
        ]);
    }

}
