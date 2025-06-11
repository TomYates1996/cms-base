<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->enum('feed_type', ['slides', 'blog', 'listings', 'events', 'products'])->nullable()->after('type');
            $table->unsignedInteger('to_show')->nullable()->after('feed_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropColumn('feed_type');
            $table->dropColumn('to_show');
        });
    }
};
