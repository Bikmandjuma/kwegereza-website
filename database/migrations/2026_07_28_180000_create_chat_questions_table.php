<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->nullable();
            $table->string('keywords')->nullable(); // comma-separated extra match terms
            $table->string('language', 5)->default('rw');
            $table->string('status')->default('active'); // active | inactive
            $table->unsignedInteger('times_matched')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->index(['status', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_questions');
    }
};
