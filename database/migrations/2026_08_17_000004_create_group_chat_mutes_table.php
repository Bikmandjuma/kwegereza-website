<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_chat_mutes', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('actor_type'); // 'student' | 'owner' — who is muted
            $table->unsignedBigInteger('actor_id');
            $table->unsignedBigInteger('muted_by'); // owner id who issued the mute
            $table->timestamp('muted_until')->nullable(); // null = indefinite, until unmuted
            $table->timestamps();

            $table->unique(['group', 'actor_type', 'actor_id'], 'group_mute_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_chat_mutes');
    }
};
