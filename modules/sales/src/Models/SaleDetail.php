<?php

declare(strict_types=1);

namespace SynApps\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Sales\Factories\SaleDetailFactory;
use SynApps\Modules\Production\Models\Product;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        "sale_id",
        "product_id",
        "qty",
        "price",
        "subtotal",
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return SaleDetailFactory::new();
    }
}
