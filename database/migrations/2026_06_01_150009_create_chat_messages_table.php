<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(){
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->string('guest_id'); // Unique session ID for the guest
            $table->string('sender_type'); // guest or admin
            $table->string('sender_name'); // Guest name or Admin
            $table->text('message'); // Chat content
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
