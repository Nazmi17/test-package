<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('stock', 10, 2)->default(0)->after('cost_per_unit');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock', 10, 2)->default(0)->after('suggested_price');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('stock');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
