<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->index();
            $table->string('sub_category')->index();
            $table->text('description')->nullable();
            $table->json('tags')->nullable();

            // Contact & Location
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->string('postcode')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Media
            $table->json('media_gallery')->nullable();
            $table->string('thumbnail_image')->nullable(); 

            // Business Details
            $table->json('opening_hours')->nullable(); 
            $table->json('prices')->nullable(); 
            $table->string('booking_url')->nullable();
            $table->string('reservation_email')->nullable();

            $table->boolean('featured')->default(false);
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();

            // Extras
            $table->json('social_links')->nullable(); 
            $table->json('amenities')->nullable(); 
            $table->json('accessibility_info')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
