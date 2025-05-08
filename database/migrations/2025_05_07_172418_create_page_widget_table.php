<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('page_widget', function (Blueprint $table) {
            $table->unsignedBigInteger('page_id');
            $table->unsignedBigInteger('widget_id');
            $table->integer('position')->default(0);
            $table->enum('visibility', ['visible', 'hidden'])->default('visible');
            $table->timestamps();
    
            $table->primary(['page_id', 'widget_id']);
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
            $table->foreign('widget_id')->references('id')->on('widgets')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('page_widget');
    }
};
