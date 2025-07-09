<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfilePhotoController extends Controller
{
    /**
     * Display the specified profile photo.
     */
    public function show(Request $request, $userId): StreamedResponse
    {
        $user = User::findOrFail($userId);

        if ($request->user()->id !== $user->id && !$request->user()->hasRole('admin')) {
            abort(403, 'Unauthorized to view this photo');
        }

        if (!$user->photo || !Storage::exists($user->photo)) {
            abort(404, 'Photo not found');
        }

        $photoPath = $user->photo;
        $mimeType = Storage::mimeType($photoPath);

        return Storage::response($photoPath, null, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function current(Request $request): StreamedResponse
    {
        $user = $request->user();

        // Check if user has a photo
        if (!$user->photo || !Storage::exists($user->photo)) {
            abort(404, 'Photo not found');
        }

        $photoPath = $user->photo;
        $mimeType = Storage::mimeType($photoPath);

        return Storage::response($photoPath, null, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
