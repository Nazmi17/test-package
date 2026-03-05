<?php

declare(strict_types=1);

namespace SynApps\Modules\Purchasing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Purchasing\Factories\PurchaseFactory;
use VmEngine\Synapse\Traits\WithDeleteToken;

class Purchase extends Model
{
    use HasFactory, WithDeleteToken;

    protected $fillable = [
        'invoice_number',
        'supplier_name',
        'purchase_date',
        'total_amount',
        'status',
        'payment_status',
    ];

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return PurchaseFactory::new();
    }
}
