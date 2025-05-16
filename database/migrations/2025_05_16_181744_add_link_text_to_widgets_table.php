<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->string('link_text')->nullable()->after('link'); 
        });
    }
    
    public function down()
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropColumn('link_text');
        });
    }
};
