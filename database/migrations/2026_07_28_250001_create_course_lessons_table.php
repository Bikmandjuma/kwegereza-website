<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable();       // standalone text content, if not linked to a Darsat
            $table->unsignedBigInteger('darsat_id')->nullable(); // optional link to an existing audio lesson
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('darsat_id')->references('id')->on('darsat_tables')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};
