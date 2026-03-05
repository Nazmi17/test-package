<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('labor_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('payment_type', ['borongan', 'harian']);
            $table->decimal('default_cost', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('labor_services');
    }
};
