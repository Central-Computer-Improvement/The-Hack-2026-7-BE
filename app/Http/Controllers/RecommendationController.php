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
                'error' => 'Gemini API request failed',
                'status' => $httpCode
            ];
        }

        $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (! $text) {
            return [
                'error' => 'Invalid response from Gemini'
            ];
        }

        $text = trim($text);
        $text = preg_replace('/^```json\s*/', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);
        $text = trim($text);

        $result = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to parse Gemini JSON', [
                'response' => $text,
                'json_error' => json_last_error_msg()
            ]);

            return [
                'error' => 'Gemini returned invalid JSON'
            ];
        }

        return $result;
    }

    public function getJobRecommendations(Request $request)
    {
        $user = $request->user ?? null;

        if (! $user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $skills = $user->skills
            ->pluck('name')
            ->toArray();

        $prompt = "You are an assistant that recommends suitable job titles and short reasons based on a user's profile.\n";

        $prompt .= "User: name=" . ($user->name ?? 'someone')
            . ", role=" . ($user->role ?? 'someone')
            . ", education=" . ($user->education ?? 'someone')
            . ", skills=" . implode(', ', $skills) . "\n";

        $prompt .= "Return ONLY a valid JSON array. Do not use markdown or code fences.\n";
        $prompt .= "Each object must contain: job_title, position, matched_skills.\n";
        $prompt .= "Limit to 4 items.";

        $recommendations = $this->callGemini($prompt);

        if (isset($recommendations['error'])) {
            return response()->json($recommendations, 500);
        }

        return response()->json([
            'recommendations' => $recommendations
        ]);
    }

    public function getCourseRecommendations(Request $request)
    {
        $user = $request->user ?? null;

        if (! $user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $skills = $user->skills
            ->pluck('name')
            ->toArray();

        $prompt = "You are an assistant that recommends training courses for a user to reach target career goals.\n";

        $prompt .= "User: name=" . ($user->name ?? 'someone')
            . ", role=" . ($user->role ?? 'someone')
            . ", education=" . ($user->education ?? 'someone')
            . ", skills=" . implode(', ', $skills) . "\n";

        $prompt .= "Return ONLY a valid JSON array. Do not use markdown or code fences.\n";
        $prompt .= "Each object must contain: course_title, provider, duration_estimate, skills_gained, link.\n";
        $prompt .= "Limit to 4 items.";

        $courses = $this->callGemini($prompt);

        if (isset($courses['error'])) {
            return response()->json($courses, 500);
        }

        return response()->json([
            'courses' => $courses
        ]);
    }
}