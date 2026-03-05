<?php

declare(strict_types=1);

namespace SynApps\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Inventory\Factories\StockLedgerFactory;

class StockLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_type',
        'item_id',
        'transaction_type',
        'reference_type',
        'reference_id',
        'qty',
        'balance_after',
        'description',
    ];

    public function item()
    {
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return StockLedgerFactory::new();
    }
}
