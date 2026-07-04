<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('darsat_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teachers');
            $table->string('title');
            $table->string('type');
            $table->string('audio');
            $table->timestamps();
            $table->foreign('teachers')
                  ->references('id')
                  ->on('owners')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('darsat_tables');
    }
};
