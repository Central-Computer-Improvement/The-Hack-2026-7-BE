<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_skills', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('skill_id')
                ->constrained('skills')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_skills', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['skill_id']);

            $table->dropColumn(['user_id', 'skill_id']);
        });
    }
};
