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
        Schema::table('headers', function (Blueprint $table) {
            $table->unsignedInteger('order')->nullable()->after('section');
            $table->index('order');
        });
    
        Schema::table('footers', function (Blueprint $table) {
            $table->unsignedInteger('order')->nullable()->after('section');
            $table->index('order');
        });
    
        Schema::table('widgets', function (Blueprint $table) {
            $table->unsignedInteger('order')->nullable()->after('type');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('headers', function (Blueprint $table) {
            $table->dropIndex(['order']);
            $table->dropColumn('order');
        });
    
        Schema::table('footers', function (Blueprint $table) {
            $table->dropIndex(['order']);
            $table->dropColumn('order');
        });
    
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropIndex(['order']);
            $table->dropColumn('order');
        });
    }
};
