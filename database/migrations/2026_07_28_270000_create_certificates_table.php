<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();

            $table->string('certificate_number')->unique(); // e.g. KIU-2026-000123
            $table->string('verification_code')->unique();  // opaque, used in the public verify URL / QR code

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id')->nullable(); // which course this certifies, if course-based

            $table->string('title'); // e.g. "Certificate of Completion — Islamic Basics"

            $table->unsignedBigInteger('issued_by')->nullable(); // owner_id, null if auto-issued by the system
            $table->timestamp('issued_at');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
