<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();

            // Optional attachment point — a quiz can stand alone, or hang
            // off a CourseLesson or a DarsatTable lesson. Polymorphic so
            // adding a third attachable type later needs no schema change.
            $table->nullableMorphs('quizzable');

            $table->unsignedInteger('passing_percentage')->default(70);
            $table->string('status')->default('draft'); // draft | published

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
