<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('footers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('page_id')
                ->constrained()
                ->onDelete('cascade');

            $table->enum('section', ['primary', 'secondary', 'footer']);

            $table->foreignId('logo_id')
                ->nullable()
                ->constrained('images')
                ->onDelete('set null');


            $table->boolean('is_saved')->default(false);
            $table->string('name')->nullable(); 

            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('footers')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('footers');
    }
};
