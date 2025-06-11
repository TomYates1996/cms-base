<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('listings', 'sub_category')) {
                $table->dropColumn('sub_category');
            }

            $table->unsignedBigInteger('category_id')->nullable()->after('id');
            $table->unsignedBigInteger('sub_category_id')->nullable()->after('category_id');

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('sub_category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn(['category_id', 'sub_category_id']);
            
            $table->string('category')->nullable()->after('id');
            $table->string('sub_category')->nullable()->after('category');
        });
    }
};
