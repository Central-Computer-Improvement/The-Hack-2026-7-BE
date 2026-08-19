<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $user->load('skills', 'company');

        return response()->json([
            'message' => 'Profil berhasil diambil',
            'data' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'education' => ['sometimes', 'nullable', 'string', 'max:100'],
            'bio' => ['sometimes', 'nullable', 'string'],
        ]);

        $user->update($validated);

        $user->load('skills', 'company');

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'data' => $user,
        ]);
    }
}
