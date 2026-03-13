<?php

declare(strict_types=1);

namespace SynApps\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Inventory\Factories\MaterialFactory;
use VmEngine\Synapse\Traits\WithDeleteToken;

class Material extends Model
{
    use HasFactory;
    use WithDeleteToken;

    protected $fillable = ['code', 'name', 'unit', 'cost_per_unit', 'stock'];

    public function ledgers()
    {
        return $this->morphMany(StockLedger::class, 'item');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return MaterialFactory::new();
    }
}
