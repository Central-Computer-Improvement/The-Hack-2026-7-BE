<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $notifications = Notification::where('user_id', $request->user()->id)
        ->latest()
        ->get();

    return response()->json([
        'message' => 'Daftar notifikasi berhasil diambil',
        'data' => $notifications
    ]);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $notification = Notification::where('user_id', $request->user()->id)
    ->findOrFail($id);

    $notification->update([
        'is_read' => true,
    ]);

    return response()->json([
        'message' => 'Notifikasi berhasil ditandai sebagai sudah dibaca',
        'data' => [
            'id' => $notification->id,
            'is_read' => $notification->is_read,
        ]
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
