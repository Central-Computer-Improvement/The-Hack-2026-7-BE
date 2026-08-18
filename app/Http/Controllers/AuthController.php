<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    public function register(Request $request)
    {
        $data = $request->only(['name', 'email', 'password']);

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return response()->json(['message' => 'Missing required fields'], 422);
        }

        if (User::where('email', $data['email'])->exists()) {
            return response()->json(['message' => 'Email already registered'], 409);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
        ]);

        $tokenData = $this->createToken($user);

        return response()->json(['token' => $tokenData['token'], 'payload' => $tokenData['payload'], 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $data = $request->only(['email', 'password']);

        if (empty($data['email']) || empty($data['password'])) {
            return response()->json(['message' => 'Missing credentials'], 422);
        }

        $user = User::where('email', $data['email'])->first();
        if (! $user || ! password_verify($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $tokenData = $this->createToken($user);

        return response()->json(['token' => $tokenData['token'], 'payload' => $tokenData['payload'], 'user' => $user]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user ?? null);
    }

    protected function createToken(User $user)
    {
        $now = time();
        $exp = $now + (60 * 60 * 24); // 24 hours

        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'role' => $user->role ?? null,
            'iat' => $now,
            'exp' => $exp,
            'jti' => bin2hex(random_bytes(8)),
        ];

        $secret = config('app.jwt_secret') ?: env('JWT_SECRET', 'change_me');

        $token = JWT::encode($payload, $secret, 'HS256');

        return ['token' => $token, 'payload' => $payload];
    }
}
