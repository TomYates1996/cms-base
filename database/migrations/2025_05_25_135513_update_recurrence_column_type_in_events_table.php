<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('recurrence')->collation('utf8mb4_unicode_ci')->change();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('recurrence')->collation('utf8mb4_bin')->change();
        });
    }
};
