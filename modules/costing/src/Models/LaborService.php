<?php

declare(strict_types=1);

namespace SynApps\Modules\Costing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Costing\Factories\LaborServiceFactory;
use VmEngine\Synapse\Traits\WithDeleteToken;

class LaborService extends Model
{
    use HasFactory;
    use WithDeleteToken;

    protected $fillable = [
        'name',
        'payment_type',
        'default_cost',
    ];

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return LaborServiceFactory::new();
    }
}
