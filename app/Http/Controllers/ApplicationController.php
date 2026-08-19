<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Notification;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Lamaran milik pengguna, atau — bagi akun perusahaan — lamaran yang masuk
     * ke lowongan perusahaan tersebut.
     *
     * Sebelumnya selalu disaring dengan `user_id`, sehingga perusahaan tidak
     * pernah bisa melihat siapa yang melamar lowongannya.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Application::with(['user', 'jobPosting.company']);

        if ($this->actsAsCompany($user)) {
            $query->whereHas(
                'jobPosting',
                fn ($job) => $job->where('company_id', $user->company_id)
            );
        } else {
            $query->where('user_id', $user->id);
        }

        return response()->json([
            'message' => 'Daftar lamaran berhasil diambil',
            'data' => $query->latest()->get(),
        ]);
    }

    /** Hanya akun ber-role `company` yang benar-benar tertaut ke sebuah perusahaan. */
    protected function actsAsCompany($user): bool
    {
        return $user->role === 'company' && $user->company_id !== null;
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
                'message' => 'Anda sudah melamar lowongan ini',
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
            'data' => $application->load(['user', 'jobPosting']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $application = Application::with([
            'user',
            'jobPosting.company',
        ])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'message' => 'Detail lamaran berhasil diambil',
            'data' => $application,
        ]);
    }

    /**
     * Perusahaan mengubah status lamaran; pelamar hanya boleh menyunting pesan
     * pengantarnya sendiri.
     *
     * Sebelumnya keduanya disaring dengan `user_id`, artinya justru pelamar
     * yang bisa menaikkan statusnya sendiri menjadi "Accepted" sementara
     * perusahaan tidak bisa menyentuhnya sama sekali.
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $isCompany = $this->actsAsCompany($user);

        $query = Application::query();

        if ($isCompany) {
            $query->whereHas(
                'jobPosting',
                fn ($job) => $job->where('company_id', $user->company_id)
            );
        } else {
            $query->where('user_id', $user->id);
        }

        $application = $query->findOrFail($id);

        $validated = $request->validate([
            'status' => ['sometimes', 'in:Pending,Reviewed,Interview,Accepted,Rejected'],
            'message' => ['sometimes', 'nullable', 'string'],
        ]);

        if (! $isCompany && isset($validated['status'])) {
            return response()->json([
                'message' => 'Hanya perusahaan pemilik lowongan yang dapat mengubah status lamaran',
            ], 403);
        }

        $oldStatus = $application->status;

        $application->update($validated);

        if (
            isset($validated['status']) &&
            $validated['status'] !== $oldStatus
        ) {
            Notification::create([
                'user_id' => $application->user_id,
                'type' => 'application_status_updated',
                'message' => 'Status lamaran Anda berubah menjadi '.$application->status.'.',
                'is_read' => false,
            ]);
        }

        return response()->json([
            'message' => 'Lamaran berhasil diperbarui',
            'data' => $application->fresh()->load([
                'user',
                'jobPosting.company',
            ]),
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
            'message' => 'Lamaran berhasil dihapus',
        ]);
    }
}
