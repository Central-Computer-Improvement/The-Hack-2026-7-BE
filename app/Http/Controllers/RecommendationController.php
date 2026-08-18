<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class RecommendationController
{
    protected function callGemini(string $prompt)
    {
        $apiKey = env('GEMINI_API_KEY');
        $endpoint = env('GEMINI_ENDPOINT');

        if (! $apiKey) {
            return ['error' => 'GEMINI_API_KEY not configured'];
        }

        $payload = json_encode([
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt
                        ]
                    ]
                ]
            ]
        ]);

        $ch = curl_init($endpoint);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $apiKey,
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $resp = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($err) {
            Log::error('Gemini request error: ' . $err);

            return [
                'error' => $err
            ];
        }

        $decoded = json_decode($resp, true);

        if ($httpCode >= 400) {
            Log::error('Gemini API error', [
                'status' => $httpCode,
                'response' => $decoded
            ]);

            return [
                'error' => $decoded
            ];
        }

        return $decoded ?? ['raw' => $resp];
    }
    public function getJobRecommendations(Request $request)
    {
        $user = $request->user ?? null;
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $prompt = "You are an assistant that recommends suitable job titles and short reasons based on a user's profile.\n";
        $prompt .= "User: name=" . ($user->name ?? 'unknown') . ", role=" . ($user->role ?? 'unknown') . ", education=" . ($user->education ?? 'unknown') . ", skills=" . implode(', ', $user->skills->pluck('name')->toArray()) . "\n";
        $prompt .= "Return a JSON array of objects with fields: title, reason, matched_skills. Limit to 8 items.";

        $resp = $this->callGemini($prompt);
        return response()->json(['request_prompt' => $prompt, 'ai' => $resp]);
    }

    public function getCourseRecommendations(Request $request)
    {
        $user = $request->user ?? null;
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $prompt = "You are an assistant that recommends training courses for a user to reach target career goals.\n";
        $prompt .= "User: name=" . ($user->name ?? 'unknown') . ", role=" . ($user->role ?? 'unknown') . ", education=" . ($user->education ?? 'unknown') . ", skills=" . implode(', ', $user->skills->pluck('name')->toArray()) . "\n";
        $prompt .= "Return a JSON array of objects with fields: course_title, provider, duration_estimate, skills_gained, reason. Limit to 6 items.";

        $resp = $this->callGemini($prompt);
        return response()->json(['request_prompt' => $prompt, 'ai' => $resp]);
    }
}

