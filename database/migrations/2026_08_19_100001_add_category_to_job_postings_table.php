<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Category is how the frontend's main navigation browses jobs (the sticky
 * category bar and the "Cari Kerja" menu both filter by it), but
 * `job_postings` had no column for it, so that filter could only ever be a
 * client-side text match over the title and description.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('category', 100)->nullable()->after('description');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });
    }
};
