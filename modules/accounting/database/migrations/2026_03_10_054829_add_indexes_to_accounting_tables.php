<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->index('account_code');
            $table->index('account_name');
        });

        Schema::table('journals', function (Blueprint $table) {
            $table->index('journal_number');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('expense_number');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropIndex('account_code');
            $table->dropIndex('account_name');
        });

        Schema::table('journals', function (Blueprint $table) {
            $table->dropIndex('journal_number');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expense_number');
        });
    }
};
