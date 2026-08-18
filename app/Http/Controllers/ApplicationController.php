<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Notification;
use App\Models\JobPosting;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $applications = Application::with([
        'user',
        'jobPosting.company'
    ])
    ->where('user_id', $request->user()->id)
    ->get();

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
        'job_posting_id' => ['required', 'exists:job_postings,id'],
        'message' => ['nullable', 'string'],
    ]);

    $existingApplication = Application::where('user_id', $request->user()->id)
        ->where('job_posting_id', $validated['job_posting_id'])
        ->first();

    if ($existingApplication) {
        return response()->json([
            'message' => 'Anda sudah melamar lowongan ini'
        ], 409);
    }

    $application = Application::create([
    'user_id' => $request->user()->id,
    'job_posting_id' => $validated['job_posting_id'],
    'message' => $validated['message'] ?? null,
    'status' => 'Pending',
    'applied_date' => now(),
]);

Notification::create([
    'user_id' => $request->user()->id,
    'type' => 'application_created',
    'message' => 'Lamaran Anda berhasil dikirim.',
    'is_read' => false,
]);

return response()->json([
    'message' => 'Lamaran berhasil dibuat',
    'data' => $application->load(['user', 'jobPosting'])
], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $application = Application::with([
             'user',
             'jobPosting.company'
    ])
        ->where('user_id', $request->user()->id)
        ->findOrFail($id);

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
    $application = Application::where('user_id', $request->user()->id)
        ->findOrFail($id);

    $validated = $request->validate([
        'status' => ['sometimes', 'in:Pending,Reviewed,Interview,Accepted,Rejected'],
        'message' => ['sometimes', 'nullable', 'string'],
    ]);

    $oldStatus = $application->status;

    $application->update($validated);

    if (
        isset($validated['status']) &&
        $validated['status'] !== $oldStatus
    ) {
        Notification::create([
            'user_id' => $application->user_id,
            'type' => 'application_status_updated',
            'message' => 'Status lamaran Anda berubah menjadi ' . $application->status . '.',
            'is_read' => false,
        ]);
    }

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
    public function destroy(Request $request, string $id)
    {
    $application = Application::where('user_id', $request->user()->id)
        ->findOrFail($id);

    $application->delete();

    return response()->json([
        'message' => 'Lamaran berhasil dihapus'
    ]);
    }
}
