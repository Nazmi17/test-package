<?php

declare(strict_types=1);

namespace SynApps\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Sales\Factories\SaleFactory;
use VmEngine\Synapse\Traits\WithDeleteToken;

class Sale extends Model
{
    use HasFactory, WithDeleteToken;

    protected $fillable = [
        "invoice_number",
        "customer_name",
        "sale_date",
        "total_amount",
        "status",
        "payment_status",
    ];

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return SaleFactory::new();
    }
}
