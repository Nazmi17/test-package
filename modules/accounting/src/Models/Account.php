<?php

declare(strict_types=1);

namespace SynApps\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Accounting\Factories\AccountFactory;
use VmEngine\Synapse\Traits\WithDeleteToken;

class Account extends Model
{
    use HasFactory;
    use WithDeleteToken;

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'is_system_default',
    ];

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return AccountFactory::new();
    }
}
