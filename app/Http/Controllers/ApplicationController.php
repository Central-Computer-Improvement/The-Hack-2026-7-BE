<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $applications = Application::with([
            'user',
            'jobPosting.company'
        ])->get();

        return response()->json([
            'message' => 'Daftar lamaran berhasil diambil',
            'data' => $applications
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validated = $request->validate([
        'user_id' => ['required', 'exists:users,id'],
        'job_posting_id' => ['required', 'exists:job_postings,id'],
        'message' => ['nullable', 'string'],
        'status' => ['nullable', 'in:Pending,Reviewed,Interview,Accepted,Rejected'],
    ]);

    $application = Application::create([
        'user_id' => $validated['user_id'],
        'job_posting_id' => $validated['job_posting_id'],
        'message' => $validated['message'] ?? null,
        'status' => $validated['status'] ?? 'Pending',
        'applied_date' => now(),
    ]);

    return response()->json([
        'message' => 'Lamaran berhasil dibuat',
        'data' => $application->load(['user', 'jobPosting'])
    ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $application = Application::with([
        'user',
        'jobPosting.company'
    ])->findOrFail($id);

    return response()->json([
        'message' => 'Detail lamaran berhasil diambil',
        'data' => $application
    ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    $application = Application::findOrFail($id);

    $validated = $request->validate([
        'status' => ['sometimes', 'in:Pending,Reviewed,Interview,Accepted,Rejected'],
        'message' => ['sometimes', 'nullable', 'string'],
    ]);

    $application->update($validated);

    return response()->json([
        'message' => 'Lamaran berhasil diperbarui',
        'data' => $application->fresh()->load([
            'user',
            'jobPosting.company'
        ])
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
    $application = Application::findOrFail($id);

    $application->delete();

    return response()->json([
        'message' => 'Lamaran berhasil dihapus'
    ]);
    }
}
