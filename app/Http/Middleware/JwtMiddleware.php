<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');
        if (! $authHeader || ! preg_match('/Bearer\s+(\S+)/', $authHeader, $m)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = $m[1];
        $secret = config('app.jwt_secret') ?: env('JWT_SECRET', 'change_me');

        try {
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token', 'error' => $e->getMessage()], 401);
        }

        $userId = $decoded->sub ?? null;

        if (! $userId) {
            return response()->json(['message' => 'Invalid token payload'], 401);
        }

        $user = User::find($userId);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
