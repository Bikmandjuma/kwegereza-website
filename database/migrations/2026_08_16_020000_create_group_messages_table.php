<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Student/Leader Groups phase: real net-new functionality — confirmed
 * absent in the Phase 0 audit (grep for "group chat" found nothing at
 * all: no model, route, or migration). Three fixed groups per spec
 * section 19: male students, female students, leaders — not user-created
 * groups, so no separate "groups" table is needed, just a discriminator.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_messages', function (Blueprint $table) {
            $table->id();
            $table->string('group'); // 'male_students' | 'female_students' | 'leaders'
            $table->string('sender_type'); // 'student' | 'owner'
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_name');
            $table->text('message');
            $table->timestamps();

            $table->index(['group', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_messages');
    }
};
