<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('author')->nullable()->after('title');
            $table->string('category')->nullable()->after('author');
            $table->text('description')->nullable()->after('category');
            $table->string('cover_image')->nullable()->after('book');
            $table->string('status')->default('published')->after('cover_image'); // draft | published
            $table->boolean('is_downloadable')->default(true)->after('status');
            $table->unsignedInteger('views')->default(0)->after('is_downloadable');

            $table->unsignedBigInteger('created_by')->nullable()->after('views');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            $table->timestamp('published_at')->nullable()->after('updated_by');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'author', 'category', 'description', 'cover_image', 'status',
                'is_downloadable', 'views', 'created_by', 'updated_by', 'published_at',
            ]);
        });
    }
};
