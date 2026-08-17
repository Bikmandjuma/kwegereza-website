<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kwegereza Chat phase: the guest widget's own JS (twandikire.blade.php)
 * already polls GET /chat/admin-typing/{guestId} to show "admin arandika"
 * — but that route never existed, and the `chat_presence` table only ever
 * had a single `typing` column with no way to represent "guest is typing"
 * and "admin is typing" independently. This adds the missing column so
 * both directions can actually be tracked.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_presence', function (Blueprint $table) {
            $table->boolean('admin_typing')->default(false)->after('typing');
        });
    }

    public function down(): void
    {
        Schema::table('chat_presence', function (Blueprint $table) {
            $table->dropColumn('admin_typing');
        });
    }
};
