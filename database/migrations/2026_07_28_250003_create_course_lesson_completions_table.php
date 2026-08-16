<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_lesson_id');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_lesson_id')->references('id')->on('course_lessons')->onDelete('cascade');
            $table->unique(['user_id', 'course_lesson_id'], 'course_lesson_completion_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lesson_completions');
    }
};
