<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /** A couple of notifications for the demo applicant, so /notifications isn't empty. */
    public function run(): void
    {
        $applicant = User::where('role', 'applicant')->first();

        if (! $applicant) {
            $this->command?->warn('NotificationSeeder skipped: no applicant found. Run UserSeeder first.');

            return;
        }

        Notification::create([
            'user_id' => $applicant->id,
            'type' => 'application_created',
            'message' => 'Lamaran Anda untuk Frontend Developer berhasil dikirim.',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $applicant->id,
            'type' => 'application_status_updated',
            'message' => 'Status lamaran Anda berubah menjadi Reviewed.',
            'is_read' => false,
        ]);
    }
}
