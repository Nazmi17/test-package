<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name'); 
            $table->decimal('markup_percentage', 5, 2)->default(30); 
            $table->decimal('total_hpp', 15, 2)->default(0); 
            $table->decimal('suggested_price', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('product_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->decimal('quantity_required', 10, 4); 
            $table->decimal('waste_percentage', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('product_labors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('labor_service_id')->constrained('labor_services')->onDelete('cascade');
            $table->decimal('cost', 15, 2); 
            $table->timestamps();
        });

        Schema::create('product_overheads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('overhead_id')->constrained('overheads')->onDelete('cascade');
            $table->decimal('allocated_cost', 15, 2); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_overheads');
        Schema::dropIfExists('product_labors');
        Schema::dropIfExists('product_materials');
        Schema::dropIfExists('products');
    }
};
