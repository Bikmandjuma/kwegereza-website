<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Spec §27 (replies, pinned messages) and §28 (soft deletion with
 * scheduled cleanup, "do NOT blindly permanently delete everything")
 * both need columns this table never had. Added as one migration since
 * they're small, additive, and share the same table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_messages', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('sender_name')
                ->constrained('group_messages')->nullOnDelete();

            $table->timestamp('pinned_at')->nullable()->after('message');
            $table->string('pinned_by_type')->nullable()->after('pinned_at'); // 'owner' — only leaders/admin pin
            $table->unsignedBigInteger('pinned_by_id')->nullable()->after('pinned_by_type');

            $table->softDeletes(); // deleted_at — retention cleanup purges these later, doesn't hard-delete on the spot
        });
    }

    public function down(): void
    {
        Schema::table('group_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn(['pinned_at', 'pinned_by_type', 'pinned_by_id', 'deleted_at']);
        });
    }
};
