<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Public course catalogue backing the frontend's /kursus page.
     *
     * Supports the same filters that page offers: free-text search plus
     * category, format and duration.
     */
    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        if ($request->filled('duration')) {
            $query->where('duration', $request->duration);
        }

        return response()->json([
            'message' => 'Daftar kursus berhasil diambil',
            'data' => $query->latest()->get(),
        ]);
    }

    public function show(string $id)
    {
        $course = Course::findOrFail($id);

        return response()->json([
            'message' => 'Detail kursus berhasil diambil',
            'data' => $course,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        $course = Course::create($validated);

        return response()->json([
            'message' => 'Kursus berhasil dibuat',
            'data' => $course,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $course = Course::findOrFail($id);

        $course->update($this->validatePayload($request, partial: true));

        return response()->json([
            'message' => 'Kursus berhasil diperbarui',
            'data' => $course->fresh(),
        ]);
    }

    public function destroy(string $id)
    {
        Course::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Kursus berhasil dihapus',
        ]);
    }

    /**
     * `video_url` takes either a YouTube link or a direct video file URL, so
     * it's validated as a plain URL rather than pinned to one host.
     */
    protected function validatePayload(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';
        $optional = 'sometimes';

        return $request->validate([
            'title' => [$required, 'string', 'max:150'],
            'description' => [$required, 'string'],
            'category' => [$optional, 'nullable', 'string', 'max:100'],
            'format' => [$optional, 'in:Video,Quiz'],
            'duration' => [$optional, 'nullable', 'string', 'max:50'],
            'rating' => [$optional, 'nullable', 'numeric', 'min:0', 'max:5'],
            'thumbnail_url' => [$optional, 'nullable', 'url', 'max:255'],
            'video_url' => [$optional, 'nullable', 'url'],
            'skill_id' => [$optional, 'nullable', 'exists:skills,id'],
        ]);
    }
}
