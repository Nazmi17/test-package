<?php

declare(strict_types=1);

namespace SynApps\Modules\Production\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Production\Factories\ProductFactory;
use SynApps\Modules\Inventory\Models\Material;
use SynApps\Modules\Costing\Models\LaborService;
use SynApps\Modules\Costing\Models\Overhead;
use VmEngine\Synapse\Traits\WithDeleteToken;

class Product extends Model
{
    use HasFactory;
    use WithDeleteToken;

    protected $fillable = [
        'sku',
        'name',
        'markup_percentage',
        'total_hpp',
        'suggested_price',
        'stock',
        'category_id',
    ];

    public function materials() {
        return $this->belongsToMany(Material::class, 'product_materials')
                    ->withPivot(['quantity_required', 'waste_percentage'])
                    ->withTimestamps();
    }

    public function labors() {
        return $this->belongsToMany(LaborService::class, 'product_labors')
                    ->withPivot(['cost'])
                    ->withTimestamps();
    }

    public function overheads() {
        return $this->belongsToMany(Overhead::class, 'product_overheads')
                    ->withPivot(['allocated_cost'])
                    ->withTimestamps();
    }

    public function calculateHpp()
    {
        // 1. Hitung Material (Rumus: Harga x Qty x (1 + waste%))
        $materialCost = $this->materials->sum(function($item) {
            $qty = $item->pivot->quantity_required;
            $waste = $item->pivot->waste_percentage; 
            $price = $item->cost_per_unit;
            
            $totalQty = $qty * (1 + ($waste / 100));
            return $totalQty * $price;
        });

        // 2. Hitung Tenaga Kerja
        $laborCost = $this->labors->sum('pivot.cost');

        // 3. Hitung Overhead
        $overheadCost = $this->overheads->sum('pivot.allocated_cost');

        // Total HPP Murni
        $totalHpp = $materialCost + $laborCost + $overheadCost;

        // Hitung Harga Jual Saran (Suggested Price) berdasarkan Markup %
        $suggestedPrice = $totalHpp * (1 + ($this->markup_percentage / 100));

        // Simpan ke Database
        $this->update([
            'total_hpp' => $totalHpp,
            'suggested_price' => $suggestedPrice
        ]);

        return $totalHpp;
    }

    public function ledgers()
    {
        return $this->morphMany(\SynApps\Modules\Inventory\Models\StockLedger::class, 'item');
    }

    public function category() 
    {
        return $this->belongsTo(\SynApps\Modules\Inventory\Models\Category::class);
    }
    
    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return ProductFactory::new();
    }
}
