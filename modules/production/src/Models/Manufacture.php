<?php

declare(strict_types=1);

namespace SynApps\Modules\Production\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Production\Factories\ManufactureFactory;

class Manufacture extends Model
{
    use HasFactory;

    protected $fillable = [
        "manufacture_number",
        "product_id",
        "production_date",
        "qty",
        "total_hpp",
        "status",
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
        return ManufactureFactory::new();
    }
}
