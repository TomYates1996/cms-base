<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['listings', 'events', 'products'] as $tableName) {    
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'sub_category_id')) {
                    $fkName = "{$tableName}_sub_category_id_foreign";
                    DB::statement("ALTER TABLE `$tableName` DROP FOREIGN KEY `$fkName`");
                    $table->dropColumn('sub_category_id');
                }

                $table->foreignId('sub_category_id')
                    ->nullable()
                    ->constrained('sub_categories')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['listings', 'events', 'products'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign([$table->getTable().'_sub_category_id_foreign']);
                $table->dropColumn('sub_category_id');
            });
        }
    }
};
