<?php

declare(strict_types=1);

namespace SynApps\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Accounting\Factories\JournalDetailFactory;

class JournalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_id',
        'account_id',
        'debit',
        'credit',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return JournalDetailFactory::new();
    }
}
