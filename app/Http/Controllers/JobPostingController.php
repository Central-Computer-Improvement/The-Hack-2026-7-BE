<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\JobPosting;

class JobPostingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobPostings = JobPosting::with('company')->get();

        return response()->json([
            'message' => 'Daftar lowongan berhasil diambil',
            'data' => $jobPostings
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
        'company_id' => ['required', 'exists:companies,id'],
        'title' => ['required', 'string', 'max:150'],
        'description' => ['required', 'string'],
        'location' => ['required', 'string', 'max:150'],
        'salary' => ['required', 'numeric', 'min:0'],
        'employment_type' => [
            'required',
            Rule::in(['Full Time', 'Part Time', 'Internship', 'Contract']),
        ],
        'closing_date' => ['required', 'date'],
    ]);

    $jobPosting = JobPosting::create($validated);

    return response()->json([
        'message' => 'Lowongan berhasil dibuat',
        'data' => $jobPosting
    ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $jobPosting = JobPosting::with('company')->findOrFail($id);

        return response()->json([
            'message' => 'Detail lowongan berhasil diambil',
            'data' => $jobPosting
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $jobPosting = JobPosting::findOrFail($id);

    $validated = $request->validate([
        'company_id' => ['sometimes', 'exists:companies,id'],
        'title' => ['sometimes', 'string', 'max:150'],
        'description' => ['sometimes', 'string'],
        'location' => ['sometimes', 'string', 'max:150'],
        'salary' => ['sometimes', 'numeric', 'min:0'],
        'employment_type' => [
            'sometimes',
            Rule::in(['Full Time', 'Part Time', 'Internship', 'Contract']),
        ],
        'closing_date' => ['sometimes', 'date'],
    ]);

    $jobPosting->update($validated);

    return response()->json([
        'message' => 'Lowongan berhasil diperbarui',
        'data' => $jobPosting->fresh()->load('company')
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jobPosting = jobPosting::findOrFail($id);

        $jobPosting->delete();

        return response()->json([
            'message' => 'Lowongan berhasil dihapus'
        ]);
    }
}
