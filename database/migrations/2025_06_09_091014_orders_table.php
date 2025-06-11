<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->decimal('price', 10, 2);
            $table->boolean('completed')->default(false);
            $table->string('status')->default('pending'); 

            $table->string('paypal_order_id')->nullable(); 
            $table->string('paypal_capture_id')->nullable(); 
            $table->json('paypal_response')->nullable(); 

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');

            $table->date('delivery_date')->nullable();
            $table->string('delivery_method')->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('city')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();

            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
