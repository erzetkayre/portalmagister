<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $isMahasiswa = Gate::allows('mahasiswa');
        $profile = $isMahasiswa ? $user->mahasiswaProfile() : $user->dosenProfile();

        return Inertia::render('auth/Profile', [
            'gender' => $profile?->gender,
            'angkatan' => $isMahasiswa ? $profile?->angkatan : null,
            'kode_dsn' => !$isMahasiswa ? $profile?->kode_dsn : null,
            'bidang_keahlian' => !$isMahasiswa ? $profile?->bidang_keahlian : null,
            'signature_url' => $user->signature_url,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'nomor_induk' => ['required', 'string', Rule::unique('users', 'nomor_induk')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'in:L,P'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'angkatan' => ['nullable', 'integer', 'min:2000', 'max:2099'],
            'kode_dsn' => ['nullable', 'string', 'max:20'],
            'bidang_keahlian' => ['nullable', 'string', 'max:255'],
        ]);

        $data = $request->only(['nama', 'email', 'nomor_induk', 'phone']);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('profile-photos', 'public');
        }
        if ($user->email !== $data['email']) {
            $data['email_verified_at'] = null;
        }

        $user->update($data);

        if (Gate::allows('mahasiswa')) {
            $mahasiswa = $user->mahasiswaProfile();
            if ($mahasiswa) {
                $mahasiswa->update([
                    'nim' => $data['nomor_induk'],
                    'nama_mhs' => $data['nama'],
                    'gender' => $request->gender,
                    'angkatan' => $request->angkatan,
                ]);
            }
        } else {
            $dosen = $user->dosenProfile();
            if ($dosen) {
                $dosen->update([
                    'nip' => $data['nomor_induk'],
                    'nama_dsn' => $data['nama'],
                    'gender' => $request->gender,
                    'kode_dsn' => $request->kode_dsn,
                    'bidang_keahlian' => $request->bidang_keahlian,
                ]);
            }
        }

        return back()->with('success', [
            'title' => 'Profile Berhasil Diperbarui',
            'description' => 'Informasi profile Anda telah disimpan.',
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
            'password_confirmation' => ['required'],
        ]);
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', [
            'title' => 'Password Berhasil Diperbarui',
            'description' => 'Password Anda telah berhasil diubah.',
        ]);
    }

    public function updateAppearance(Request $request): RedirectResponse
    {
        $request->validate([
            'theme' => ['required', 'string', 'in:light,dark,system'],
        ]);
        $request->session()->put('theme', $request->theme);

        return back()->with('success', [
            'title' => 'Appearance Berhasil Diperbarui',
            'description' => 'Pengaturan tampilan telah disimpan.',
        ]);
    }

    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
            $user->update(['photo' => null]);
        }

        return back()->with('success', [
            'title' => 'Foto Berhasil Dihapus',
            'description' => 'Foto profile Anda telah dihapus.',
        ]);
    }

    public function updateSignature(Request $request): RedirectResponse
    {
        $request->validate([
            'signature' => ['required', 'image', 'mimes:png', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->signature) {
            Storage::disk('public')->delete($user->signature);
        }

        $user->update([
            'signature' => $request->file('signature')->store('signatures', 'public'),
        ]);

        return back()->with('success', [
            'title'       => 'Tanda Tangan Berhasil Diperbarui',
            'description' => 'Tanda tangan Anda telah disimpan.',
        ]);
    }

    public function deleteSignature(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->signature && Storage::disk('public')->exists($user->signature)) {
            Storage::disk('public')->delete($user->signature);
            $user->update(['signature' => null]);
        }

        return back()->with('success', [
            'title'       => 'Tanda Tangan Berhasil Dihapus',
            'description' => 'Tanda tangan Anda telah dihapus.',
        ]);
    }
}
