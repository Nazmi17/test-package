<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overheads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['tetap', 'variabel']);
            $table->decimal('cost_amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overheads');
    }
};
