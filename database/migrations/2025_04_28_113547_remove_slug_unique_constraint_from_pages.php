<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropUnique('pages_slug_unique');
        });
    }
    
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->unique('slug');
        });
    }
};
