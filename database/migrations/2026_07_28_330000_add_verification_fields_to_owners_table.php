<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->text('bio')->nullable();
            $table->text('credentials')->nullable(); // education, ijazah, certifications — free text
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();

            $table->foreign('verified_by')->references('id')->on('owners')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['bio', 'credentials', 'is_verified', 'verified_at', 'verified_by']);
        });
    }
};
