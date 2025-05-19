<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->string('slide_link_text')->nullable()->after('link_text');
        });
    }

    public function down(): void
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropColumn('slide_link_text');
        });
    }
};
