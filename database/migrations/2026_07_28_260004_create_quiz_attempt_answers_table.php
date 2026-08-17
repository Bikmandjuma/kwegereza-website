<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_attempt_id');
            $table->unsignedBigInteger('quiz_question_id');
            $table->unsignedBigInteger('quiz_answer_id')->nullable(); // chosen choice, for MC/true-false
            $table->string('answer_text')->nullable();                // typed answer, for short_answer
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->foreign('quiz_attempt_id')->references('id')->on('quiz_attempts')->onDelete('cascade');
            $table->foreign('quiz_question_id')->references('id')->on('quiz_questions')->onDelete('cascade');
            $table->foreign('quiz_answer_id')->references('id')->on('quiz_answers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
    }
};
