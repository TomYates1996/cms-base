<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('headers', function (Blueprint $table) {
            $table->enum('section_hamburger', ['primary', 'secondary', 'footer'])->default('primary');
        });
    }
    
    public function down()
    {
        Schema::table('headers', function (Blueprint $table) {
            $table->dropColumn('section_hamburger');
        });
    }
};
