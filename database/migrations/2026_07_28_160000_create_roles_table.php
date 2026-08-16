<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // e.g. "Islamic Leader"
            $table->string('slug')->unique(); // e.g. "islamic-leader"
            $table->string('description')->nullable();
            $table->boolean('is_super')->default(false); // bypasses all permission checks
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
