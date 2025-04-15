<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('widgets')->onDelete('cascade');
            $table->boolean('is_saved')->default(false);
        });
        
        Schema::table('headers', function (Blueprint $table) {
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('headers')->onDelete('cascade');
            $table->boolean('is_saved')->default(false);
        });

    }

    public function down(): void
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropColumn(['is_saved', 'template_id']);
        });

        Schema::table('headers', function (Blueprint $table) {
            $table->dropColumn(['is_saved', 'template_id']);
        });

    }
};
