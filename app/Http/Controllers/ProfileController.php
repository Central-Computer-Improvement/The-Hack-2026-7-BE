<?php

namespace App\Http\Controllers;

use App\Models\Skill;
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
            'skills' => ['sometimes', 'array'],
            'skills.*' => ['string', 'max:100'],
        ]);

        $user->update([
            'name' => $validated['name'] ?? $user->name,
            'phone' => $validated['phone'] ?? $user->phone,
            'education' => $validated['education'] ?? $user->education,
            'bio' => $validated['bio'] ?? $user->bio,
        ]);

        if ($request->has('skills')) {
            $skillNames = $request->input('skills', []);
            $skillIds = [];

            foreach ($skillNames as $name) {
                $name = trim((string) $name);

                if ($name === '') {
                    continue;
                }

                $skill = Skill::whereRaw(
                    'LOWER(name) = ?',
                    [mb_strtolower($name)]
                )->first();

                if (! $skill) {
                    $skill = Skill::create([
                        'name' => $name,
                    ]);
                }

                $skillIds[] = $skill->id;
            }

            $user->skills()->sync(array_values(array_unique($skillIds)));
        }

        $user->load('skills', 'company');

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'data' => $user,
        ]);
    }
}