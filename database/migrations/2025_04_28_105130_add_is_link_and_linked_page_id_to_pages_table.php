<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('is_link')->default(false); 
            $table->unsignedBigInteger('linked_page_id')->nullable()->after('is_link'); 
            $table->foreign('linked_page_id')->references('id')->on('pages')->onDelete('cascade'); 
        });
    }
    
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['linked_page_id']);
            $table->dropColumn(['is_link', 'linked_page_id']);
        });
    }
};
