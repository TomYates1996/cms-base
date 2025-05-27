<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('registration_url', 'booking_url');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->string('reservation_email')->nullable()->after('email');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('organiser_id')->nullable()->after('owner_id');
            $table->foreign('organiser_id')->references('id')->on('listings')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('booking_url', 'registration_url');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('reservation_email');
        });
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('organiser_id');
        });
    }
};
