<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manufactures', function (Blueprint $table) {
            $table->id();
            $table->string('manufacture_number')->unique(); // Contoh: MFG-20260305-123
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->date('production_date');
            $table->decimal('qty', 10, 2); // Berapa pcs yang diproduksi
            $table->decimal('total_hpp', 15, 2); // Menyimpan snapshot HPP per unit saat diproduksi
            $table->enum('status', ['draft', 'on_process', 'done'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manufactures');
    }
};
