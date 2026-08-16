<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amatangazos', function (Blueprint $table) {

            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('presenter')->nullable(); // e.g. "Sheikh ABOUBAKAR"
            $table->string('image')->nullable();

            // live | upcoming | done  (mirrors the tabs already on the guest page)
            $table->string('status')->default('upcoming');

            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->index(['status', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amatangazos');
    }
};
