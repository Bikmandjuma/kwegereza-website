<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_chat_logs', function (Blueprint $table) {
            $table->id();

            $table->string('guest_id')->nullable();   // matches the guest_visit_id cookie
            $table->string('session_id')->nullable();  // one browser tab session for this widget
            $table->string('ip')->nullable();
            $table->string('page')->nullable();         // which page the widget was opened on

            $table->text('question');
            $table->text('response');
            $table->unsignedBigInteger('matched_question_id')->nullable();
            $table->boolean('was_matched')->default(false);

            $table->timestamps();

            $table->index(['guest_id']);
            $table->index(['was_matched']);
            $table->foreign('matched_question_id')->references('id')->on('chat_questions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_chat_logs');
    }
};
