<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('item_type'); // Penanda: 'Material' atau 'Product'
            $table->unsignedBigInteger('item_id'); // ID dari material atau produk
            $table->string('transaction_type'); // 'in' (Masuk) atau 'out' (Keluar)
            $table->string('reference_type'); // 'Purchase', 'Production', 'Sales'
            $table->unsignedBigInteger('reference_id'); // ID transaksi referensinya
            $table->decimal('qty', 10, 2);
            $table->decimal('balance_after', 10, 2); // Sisa stok setelah transaksi
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ledgers');
    }
};
