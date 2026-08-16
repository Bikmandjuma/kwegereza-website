<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('darsat_tables', function (Blueprint $table) {
            $table->text('description')->nullable()->after('type');
            $table->string('thumbnail')->nullable()->after('description');
            $table->string('status')->default('published')->after('thumbnail'); // draft | published
            $table->unsignedInteger('plays')->default(0)->after('status');

            $table->unsignedBigInteger('created_by')->nullable()->after('plays');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->timestamp('published_at')->nullable()->after('updated_by');
        });
    }

    public function down(): void
    {
        Schema::table('darsat_tables', function (Blueprint $table) {
            $table->dropColumn([
                'description', 'thumbnail', 'status', 'plays',
                'created_by', 'updated_by', 'published_at',
            ]);
        });
    }
};
