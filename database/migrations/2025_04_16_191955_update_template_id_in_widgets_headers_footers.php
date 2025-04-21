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
        $table->dropForeign(['template_id']);
        $table->unsignedBigInteger('template_id')->nullable()->change();
    });

    Schema::table('headers', function (Blueprint $table) {
        $table->dropForeign(['template_id']);
        $table->unsignedBigInteger('template_id')->nullable()->change();
    });

    Schema::table('footers', function (Blueprint $table) {
        $table->dropForeign(['template_id']);
        $table->unsignedBigInteger('template_id')->nullable()->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
     Schema::table('widgets', function (Blueprint $table) {
        $table->foreign('template_id')->references('id')->on('widgets')->onDelete('cascade');
    });

    Schema::table('headers', function (Blueprint $table) {
        $table->foreign('template_id')->references('id')->on('headers')->onDelete('cascade');
    });

    Schema::table('footers', function (Blueprint $table) {
        $table->foreign('template_id')->references('id')->on('footers')->onDelete('cascade');
    });
    }
};
