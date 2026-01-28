<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use App\Models\Dosen;
use App\Models\Mahasiswa;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $user->load(['role', 'mahasiswa', 'dosen']);

        if ($user->role->nama_role === 'mahasiswa' && $user->mahasiswa) {
            $user->role_data = [
                'gender' => $user->mahasiswa->gender,
                'nama_mhs' => $user->mahasiswa->nama_mhs,
            ];
        } elseif (in_array($user->role->nama_role, ['dosen', 'koordinator', 'admin']) && $user->dosen) {
            $user->role_data = [
                'gender' => $user->dosen->gender,
                'nama_dosen' => $user->dosen->nama_dosen,
            ];
        }

        return Inertia::render('auth/Profile', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'nomor_induk' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'in:L,P'],
            'photo' => ['nullable', 'image', 'max:2048'], // 2MB max
        ]);

        $data = $request->only(['nama', 'email', 'nomor_induk', 'phone']);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::delete($user->photo);
            }

            $photoPath = $request->file('photo')->store('profile-photos', 'local');
            $data['photo'] = $photoPath;
        }

        if ($user->email !== $data['email']) {
            $data['email_verified_at'] = null;
        }

        $user->update($data);

        if ($user->role->nama_role === 'mahasiswa') {
            $mahasiswa = $user->mahasiswa;
            if ($mahasiswa) {
                $mahasiswa->update([
                    'nim' => $data['nomor_induk'],
                    'nama_mhs' => $data['nama'],
                    'gender' => $request->gender
                ]);
            }
        } elseif (in_array($user->role->nama_role, ['dosen', 'koordinator', 'admin'])) {
            $dosen = $user->dosen;
            if ($dosen) {
                $dosen->update([
                    'nip' => $data['nomor_induk'],
                    'nama_dosen' => $data['nama'],
                    'gender' => $request->gender
                ]);
            }
        }

        return back()->with('message', [
            'type' => 'success',
            'title' => 'Profile Berhasil Diperbarui',
            'message' => 'Informasi profile Anda telah disimpan.'
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('message', [
            'type' => 'success',
            'title' => 'Password Berhasil Diperbarui',
            'message' => 'Password Anda telah berhasil diubah.'
        ]);
    }

    /**
     * Update the user's appearance settings.
     */
    public function updateAppearance(Request $request): RedirectResponse
    {
        $request->validate([
            'theme' => ['required', 'string', 'in:light,dark,system'],
        ]);

        // Store theme preference in user settings or session
        $request->session()->put('theme', $request->theme);

        return back()->with('message', [
            'type' => 'success',
            'title' => 'Appearance Berhasil Diperbarui',
            'message' => 'Pengaturan tampilan telah disimpan.'
        ]);
    }

    /**
     * Delete the user's profile photo.
     */
    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->photo && Storage::exists($user->photo)) {
            Storage::delete($user->photo);
            $user->update(['photo' => null]);
        }

        return back()->with('success', [
            'title' => 'Foto Berhasil Dihapus',
            'description' => 'Foto profile Anda telah dihapus.'
        ]);
    }
}
