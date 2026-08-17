<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comment_id');
            $table->unsignedBigInteger('reported_by');
            $table->string('reason')->nullable();
            $table->string('status')->default('pending'); // pending | resolved
            $table->timestamps();

            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['comment_id', 'reported_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_reports');
    }
};
