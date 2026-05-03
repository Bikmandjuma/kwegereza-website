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
        Schema::create('owner_darsa_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sheikh_fk_id');
            $table->foreign('sheikh_fk_id')
                ->references('id')->on('owners')
                ->onDelete('cascade');
            $table->unsignedBigInteger('dataType_fk_id');
            $table->foreign('dataType_fk_id')
                ->references('id')->on('darsa_types')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_darsa_types');
    }
};
