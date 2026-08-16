<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->timestamp('starts_at')->nullable(); // null = available any time (no schedule)
            $table->unsignedInteger('duration_minutes')->nullable(); // null = untimed overall
        });

        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->unsignedInteger('time_limit_seconds')->nullable(); // null = no per-question limit
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['starts_at', 'duration_minutes']);
        });

        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropColumn('time_limit_seconds');
        });
    }
};
