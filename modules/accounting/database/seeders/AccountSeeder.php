<?php

declare(strict_types=1);

namespace SynDB\Modules\Accounting\Seeders;

use Illuminate\Database\Seeder;
use SynApps\Modules\Accounting\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $defaultAccounts = [
            // ASSET (Harta)
            ['account_code' => '1-1001', 'account_name' => 'Kas / Bank', 'account_type' => 'Asset'],
            ['account_code' => '1-1002', 'account_name' => 'Persediaan Bahan Baku', 'account_type' => 'Asset'],
            ['account_code' => '1-1003', 'account_name' => 'Persediaan Barang Produksi', 'account_type' => 'Asset'],
            ['account_code' => '1-1004', 'account_name' => 'Piutang Usaha', 'account_type' => 'Asset'],
            
            // LIABILITY (Kewajiban/Hutang)
            ['account_code' => '2-2001', 'account_name' => 'Hutang Usaha', 'account_type' => 'Liability'],
            
            // EQUITY (Modal)
            ['account_code' => '3-3001', 'account_name' => 'Modal Pemilik', 'account_type' => 'Equity'],

            // REVENUE (Pendapatan)
            ['account_code' => '4-4001', 'account_name' => 'Pendapatan Penjualan', 'account_type' => 'Revenue'],

            // EXPENSE (Beban/Biaya)
            ['account_code' => '5-5001', 'account_name' => 'Harga Pokok Penjualan (HPP)', 'account_type' => 'Expense'],
            ['account_code' => '5-5002', 'account_name' => 'Beban Tenaga Kerja Langsung', 'account_type' => 'Expense'],
            ['account_code' => '5-5003', 'account_name' => 'Beban Overhead Pabrik', 'account_type' => 'Expense'],
        ];

        foreach ($defaultAccounts as $acc) {
            Account::updateOrCreate(
                ['account_code' => $acc['account_code']],
                [
                    'account_name' => $acc['account_name'],
                    'account_type' => $acc['account_type'],
                    'is_system_default' => true // Kunci agar tidak bisa dihapus
                ]
            );
        }
    }
}
