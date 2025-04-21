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
            $table->unsignedBigInteger('page_id')->nullable()->change();
        });
        Schema::table('footers', function (Blueprint $table) {
            $table->unsignedBigInteger('page_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('headers', function (Blueprint $table) {
            $table->unsignedBigInteger('page_id')->nullable(false)->change();
        });
        Schema::table('footers', function (Blueprint $table) {
            $table->unsignedBigInteger('page_id')->nullable(false)->change();
        });
    }
};
