<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_message_id')->constrained('group_messages')->cascadeOnDelete();
            $table->string('actor_type'); // 'student' | 'owner'
            $table->unsignedBigInteger('actor_id');
            $table->string('emoji', 16);
            $table->timestamps();

            // One reaction per (message, actor, emoji) — re-tapping the same
            // emoji is handled as a toggle/remove in the service, not a
            // duplicate row.
            $table->unique(['group_message_id', 'actor_type', 'actor_id', 'emoji'], 'gm_reaction_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_message_reactions');
    }
};
