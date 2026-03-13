<?php

declare(strict_types=1);

namespace SynApps\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SynDB\Modules\Accounting\Factories\ExpenseFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_number',
        'expense_date',
        'expense_account_id',
        'payment_account_id',
        'amount',
        'description',
    ];

    public function expenseAccount() {
        return $this->belongsTo(Account::class, 'expense_account_id');
    }

    public function paymentAccount() {
        return $this->belongsTo(Account::class, 'payment_account_id');
    }

    protected function casts(): array
    {
        return [
            //
        ];
    }

    protected static function newFactory()
    {
        return ExpenseFactory::new();
    }
}
