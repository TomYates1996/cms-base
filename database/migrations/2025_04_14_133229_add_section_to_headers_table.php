<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('headers', function (Blueprint $table) {
            $table->enum('section', ['primary', 'secondary', 'footer'])
                  ->default('primary')
                  ->after('page_id'); 
        });
    }

    public function down()
    {
        Schema::table('headers', function (Blueprint $table) {
            $table->dropColumn('section');
        });
    }
};
