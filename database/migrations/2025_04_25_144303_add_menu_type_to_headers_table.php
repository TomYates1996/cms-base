<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('headers', function (Blueprint $table) {
            $table->string('menu_type')->default('dropdown'); 
        });
    }
    
    public function down()
    {
        Schema::table('headers', function (Blueprint $table) {
            $table->dropColumn('menu_type');
        });
    }
};
