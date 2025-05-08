<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('layout_widget', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('layout_id');
            $table->unsignedBigInteger('widget_id');
            $table->timestamps();
    
            $table->foreign('layout_id')->references('id')->on('layouts')->onDelete('cascade');
            $table->foreign('widget_id')->references('id')->on('widgets')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('layout_widget');
    }
};
