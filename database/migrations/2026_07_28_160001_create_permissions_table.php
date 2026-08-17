<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');            // e.g. "Create Darsat"
            $table->string('slug')->unique();  // e.g. "darsat.create"
            $table->string('group')->nullable(); // e.g. "darsat" — used to cluster the UI matrix
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
