<?php

declare(strict_types=1);

namespace SynApps\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Inventory\Factories\CategoryFactory;
use SynApps\Modules\Production\Models\Product;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function materials() {
        return $this->belongsToMany(Material::class);
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }
}
