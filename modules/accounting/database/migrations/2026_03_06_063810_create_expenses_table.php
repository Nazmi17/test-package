<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number')->unique();
                $table->date('expense_date');
                $table->foreignId('expense_account_id')->constrained('accounts')->onDelete('restrict');
                $table->foreignId('payment_account_id')->constrained('accounts')->onDelete('restrict');
                $table->decimal('amount', 15, 2);
                $table->text('description')->nullable();
                $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
