<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('icon')->default('🏅'); // emoji, keeps this dependency-free

            // A tiny rule engine rather than one-off hardcoded conditions:
            // criteria_type names the metric, criteria_value is the
            // threshold. Adding a new badge later is a data row, not code.
            $table->string('criteria_type'); // streak_days | darsat_completed | courses_completed | quizzes_passed
            $table->unsignedInteger('criteria_value');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
