<?php

declare(strict_types=1);

namespace SynApps\Modules\Costing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Costing\Factories\OverheadFactory;
use VmEngine\Synapse\Traits\WithDeleteToken;

class Overhead extends Model
{
    use HasFactory;
    use WithDeleteToken;

    protected $fillable = [
        'name',
        'type',
        'cost_amount',
    ];

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return OverheadFactory::new();
    }
}
