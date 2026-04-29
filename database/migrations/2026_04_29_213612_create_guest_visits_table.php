<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('guest_visits', function (Blueprint $table) {
            $table->id();
            $table->string('guest_id')->index();
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('last_visit_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_visits');
    }
};
