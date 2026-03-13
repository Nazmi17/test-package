<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->index('code');
            $table->index('name');
        });
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->index('reference_type');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropIndex(['code', 'name']);
        });
        Schema::table('stock_ledgers', function (Blueprint $table) {
            $table->dropIndex(['reference_type']);
        });
    }
};
