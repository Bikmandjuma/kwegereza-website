<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('owner_id')->nullable(); // who performed the action
            $table->string('action');       // created | updated | deleted
            $table->string('entity_type');  // e.g. App\Models\Amatangazo
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('entity_label')->nullable(); // human-readable, e.g. the title

            $table->json('before')->nullable();
            $table->json('after')->nullable();

            $table->string('ip')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
