<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('darsat_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('darsat_id');

            $table->string('status')->default('in_progress'); // in_progress | completed
            $table->unsignedInteger('last_position_seconds')->default(0);
            $table->unsignedInteger('times_played')->default(0);
            $table->timestamp('last_played_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'darsat_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('darsat_id')->references('id')->on('darsat_tables')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('darsat_progress');
    }
};
