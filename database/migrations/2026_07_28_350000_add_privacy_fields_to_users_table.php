<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('profile_visible')->default(true); // show real name publicly (e.g. on comments) vs "Umunyeshuri"
            $table->timestamp('deactivated_at')->nullable(); // set when an account-deletion request is approved
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_visible', 'deactivated_at']);
        });
    }
};
