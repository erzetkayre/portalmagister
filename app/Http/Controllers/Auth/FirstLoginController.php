<?php

namespace App\Http\Controllers\Auth;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class FirstLoginController extends Controller
{
    public function create() {
        $user = Auth::user();
        // dd($user);
        return Inertia::render('auth/ChangePassword',[
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    public function update(Request $request) {
        $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->password),
            'first_login' => 1,
        ]);

        return redirect()->route('dashboard')->with('success', 'Password berhasil diubah! Selamat datang.');
    }
}
