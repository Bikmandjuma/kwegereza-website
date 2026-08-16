<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique()->nullable(); // e.g. TCK-000123, filled in after insert using the real id
            $table->unsignedBigInteger('user_id');
            $table->string('subject');
            $table->string('category')->default('other'); // technical | content | account | other
            $table->string('priority')->default('medium'); // low | medium | high
            $table->string('status')->default('open'); // open | in_progress | resolved | closed
            $table->unsignedBigInteger('assigned_to')->nullable(); // owner_id
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
