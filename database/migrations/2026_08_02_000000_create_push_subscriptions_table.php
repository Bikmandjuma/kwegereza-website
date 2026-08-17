<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->text('endpoint'); // unique per browser/device push channel
            $table->string('endpoint_hash', 64)->unique(); // sha256 of endpoint, for fast/short unique lookups (endpoint itself can be very long)
            $table->string('public_key'); // p256dh
            $table->string('auth_token'); // auth
            $table->string('content_encoding')->nullable(); // aesgcm or aes128gcm, browser-reported
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
