<?php

declare(strict_types=1);

namespace SynApps\Modules\Purchasing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Purchasing\Factories\PurchaseDetailFactory;
use SynApps\Modules\Inventory\Models\Material;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'material_id',
        'qty',
        'price',
        'subtotal',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return PurchaseDetailFactory::new();
    }
}
