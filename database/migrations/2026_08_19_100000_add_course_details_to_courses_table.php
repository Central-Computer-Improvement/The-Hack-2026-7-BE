<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fills out `courses` so it can actually back the frontend's course catalogue.
 *
 * The original table only had skill_id/title/location/description/link, which
 * left no way to store what the UI shows: a category to browse by, whether the
 * lesson is a video or a quiz, how long it takes, its rating, a thumbnail, and
 * the video itself.
 *
 * Purely additive — `skill_id` and its foreign key are left untouched so this
 * can't fail on an existing database.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('category', 100)->nullable()->after('title');
            $table->enum('format', ['Video', 'Quiz'])->default('Video')->after('category');
            $table->string('duration', 50)->nullable()->after('format');
            $table->decimal('rating', 2, 1)->nullable()->after('duration');
            $table->string('thumbnail_url', 255)->nullable()->after('rating');
            // Accepts a YouTube watch/share/embed link or a direct video file
            // URL — the frontend normalises whichever form it gets.
            $table->text('video_url')->nullable()->after('thumbnail_url');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'format',
                'duration',
                'rating',
                'thumbnail_url',
                'video_url',
            ]);
        });
    }
};
