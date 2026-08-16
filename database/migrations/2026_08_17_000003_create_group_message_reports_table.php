<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_message_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_message_id')->constrained('group_messages')->cascadeOnDelete();
            $table->string('reporter_type'); // 'student' | 'owner'
            $table->unsignedBigInteger('reporter_id');
            $table->string('reason')->nullable();
            $table->string('status')->default('pending'); // pending | reviewed | dismissed
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_message_reports');
    }
};
