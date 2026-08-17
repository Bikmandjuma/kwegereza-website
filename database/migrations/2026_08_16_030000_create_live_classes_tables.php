<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Live Classroom phase: 100% net-new, confirmed unbuilt in the Phase 0
 * audit — no WebRTC library, no signaling, no schema at all. This is
 * genuinely the largest single feature left in the whole plan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('host_id'); // owners.id
            $table->string('status')->default('scheduled'); // scheduled | live | ended
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->foreign('host_id')->references('id')->on('owners')->cascadeOnDelete();
        });

        Schema::create('live_class_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_class_id')->constrained()->cascadeOnDelete();
            $table->string('participant_type'); // 'student' | 'owner'
            $table->unsignedBigInteger('participant_id');
            $table->string('role')->default('listener'); // 'host' | 'speaker' | 'listener'
            $table->boolean('hand_raised')->default(false);
            $table->boolean('is_muted')->default(true); // spec: default mic MUTED
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            // Original bug (hit in a real Windows/MySQL run, not just a
            // theoretical concern): Laravel's auto-generated index name
            // here is 'live_class_participants_live_class_id_participant_type_participant_id_index'
            // — 76 characters. MySQL's identifier length limit is 64,
            // so this failed with a genuine "Identifier name ... too
            // long" SQL error at migration time, not something that
            // would ever have shown up in the sqlite test database used
            // throughout this project (sqlite has no such limit).
            // Fixed by giving the index an explicit, short name.
            $table->index(['live_class_id', 'participant_type', 'participant_id'], 'lcp_class_type_participant_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_class_participants');
        Schema::dropIfExists('live_classes');
    }
};
