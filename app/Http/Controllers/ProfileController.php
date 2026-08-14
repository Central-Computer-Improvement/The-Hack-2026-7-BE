<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Skill;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user ?? null;
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->load('skills');
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $user = $request->user ?? null;
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'phone' => 'sometimes|nullable|string|max:20',
            'education' => 'sometimes|nullable|string|max:100',
            'bio' => 'sometimes|nullable|string',
            'skills' => 'sometimes|array',
            'skills.*' => 'integer|exists:skills,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user->update($validator->validated());

        if ($request->has('skills')) {
            $user->skills()->sync($request->input('skills'));
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->load('skills')
        ]);
    }
}
