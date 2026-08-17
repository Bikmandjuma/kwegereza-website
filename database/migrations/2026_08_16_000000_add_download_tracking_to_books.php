<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 6 (Books): adds the download tracking the Phase 0 audit flagged as
 * "NOT CONFIRMED IMPLEMENTED" and later confirmed genuinely absent — only a
 * `views` counter existed, with nothing incrementing it and no download
 * event ever logged anywhere. Spec section 14 explicitly requires tracking
 * authorized downloads.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->unsignedInteger('downloads')->default(0)->after('views');
        });

        Schema::create('book_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            // Nullable: books are publicly browsable to guests too (see
            // GuestController::books()), so a download isn't always tied to
            // an authenticated student account.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['book_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_downloads');
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('downloads');
        });
    }
};
