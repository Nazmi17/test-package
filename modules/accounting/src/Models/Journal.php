<?php

declare(strict_types=1);

namespace SynApps\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Accounting\Factories\JournalFactory;
use VmEngine\Synapse\Traits\WithDeleteToken;

class Journal extends Model
{
    use HasFactory;
    use WithDeleteToken;

    protected $fillable = [
        'journal_number',
        'transaction_date',
        'reference_type',
        'reference_id',
        'description',
    ];

    public function details()
    {
        return $this->hasMany(JournalDetail::class);
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return JournalFactory::new();
    }
}
