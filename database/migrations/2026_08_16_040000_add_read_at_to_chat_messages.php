<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Spec §23 wants a real Sent/Delivered/Read status, but the table only
     * had a boolean `is_read` — good enough for "read or not" but with no
     * timestamp, the UI can't show *when* something was read, and there
     * was nothing to hang a "delivered" state off either. Adding read_at
     * (kept in sync with is_read rather than replacing it, so existing
     * queries against is_read keep working unchanged).
     */
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->after('is_read');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });
    }
};
