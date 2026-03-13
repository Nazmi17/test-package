<?php

use Livewire\Component;
use SynApps\Modules\Accounting\Models\Account;
use SynApps\Modules\Accounting\Models\JournalDetail;
use Carbon\Carbon;

new class extends Component {
    public $startDate;
    public $endDate;

    public function mount()
    {
        // Default ke bulan berjalan (tanggal 1 sampai akhir bulan)
        $this->startDate = date('Y-m-01');
        $this->endDate = date('Y-m-t');
    }

    public function with()
    {
        // Fungsi helper untuk menghitung saldo (Balance) dari jurnal pada periode tertentu
        $getBalance = function($accountId, $normalBalance = 'debit') {
            $details = JournalDetail::where('account_id', $accountId)
                ->whereHas('journal', function($q) {
                    $q->whereBetween('transaction_date', [$this->startDate, $this->endDate]);
                })->get();
            
            $debit = $details->sum('debit');
            $credit = $details->sum('credit');
            
            return $normalBalance === 'debit' ? ($debit - $credit) : ($credit - $debit);
        };

        // 1. Ambil Akun Pendapatan (Saldo Normal: Kredit)
        $revenues = Account::where('account_type', 'Revenue')->get()->map(function($acc) use ($getBalance) {
            $acc->balance = $getBalance($acc->id, 'credit');
            return $acc;
        });
        $totalRevenue = $revenues->sum('balance');

        // 2. Ambil Akun HPP (Saldo Normal: Debit)
        $cogsAccounts = Account::where('account_code', '5-5001')->get()->map(function($acc) use ($getBalance) {
            $acc->balance = $getBalance($acc->id, 'debit');
            return $acc;
        });
        $totalCogs = $cogsAccounts->sum('balance');

        // Laba Kotor (Gross Profit)
        $grossProfit = $totalRevenue - $totalCogs;

        // 3. Ambil Akun Beban/Biaya Operasional lainnya (Saldo Normal: Debit)
        $expenseAccounts = Account::where('account_type', 'Expense')->where('account_code', '!=', '5-5001')->get()->map(function($acc) use ($getBalance) {
            $acc->balance = $getBalance($acc->id, 'debit');
            return $acc;
        });
        $totalExpense = $expenseAccounts->sum('balance');

        // Laba Bersih (Net Profit)
        $netProfit = $grossProfit - $totalExpense;

        return compact('revenues', 'totalRevenue', 'cogsAccounts', 'totalCogs', 'grossProfit', 'expenseAccounts', 'totalExpense', 'netProfit');
    }
};
?>

<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-end mb-8 border-b pb-4">
        <div>
            <h2 class="text-2xl font-bold uppercase tracking-wider text-gray-800">Laporan Laba Rugi</h2>
            <p class="text-gray-500 text-sm mt-1">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>
        
        <div class="flex gap-2">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Dari Tanggal</label>
                <input wire:model.live="startDate" type="date" class="border px-3 py-2 rounded-lg text-sm bg-gray-50">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Sampai Tanggal</label>
                <input wire:model.live="endDate" type="date" class="border px-3 py-2 rounded-lg text-sm bg-gray-50">
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div>
            <h3 class="font-bold text-lg text-blue-800 border-b-2 border-blue-200 mb-2">Pendapatan</h3>
            <table class="w-full text-sm">
                @foreach($revenues as $acc)
                    @if($acc->balance > 0)
                    <tr>
                        <td class="py-2 text-gray-700">{{ $acc->account_code }} - {{ $acc->account_name }}</td>
                        <td class="py-2 text-right font-semibold">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                @endforeach
                <tr class="border-t font-bold text-base">
                    <td class="py-3">Total Pendapatan</td>
                    <td class="py-3 text-right text-blue-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div>
            <h3 class="font-bold text-lg text-red-800 border-b-2 border-red-200 mb-2">Harga Pokok Penjualan (HPP)</h3>
            <table class="w-full text-sm">
                @foreach($cogsAccounts as $acc)
                    @if($acc->balance > 0)
                    <tr>
                        <td class="py-2 text-gray-700">{{ $acc->account_code }} - {{ $acc->account_name }}</td>
                        <td class="py-2 text-right font-semibold">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                @endforeach
                <tr class="border-t font-bold text-base">
                    <td class="py-3">Total HPP</td>
                    <td class="py-3 text-right text-red-700">(Rp {{ number_format($totalCogs, 0, ',', '.') }})</td>
                </tr>
            </table>
        </div>

        <div class="bg-blue-50 p-4 rounded-lg flex justify-between items-center border border-blue-200">
            <h3 class="font-bold text-lg uppercase tracking-wider text-blue-900">Laba Kotor (Gross Profit)</h3>
            <span class="font-bold text-xl text-blue-900">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
        </div>

        <div>
            <h3 class="font-bold text-lg text-orange-800 border-b-2 border-orange-200 mb-2">Beban & Biaya Operasional</h3>
            <table class="w-full text-sm">
                @foreach($expenseAccounts as $acc)
                    @if($acc->balance > 0)
                    <tr>
                        <td class="py-2 text-gray-700">{{ $acc->account_code }} - {{ $acc->account_name }}</td>
                        <td class="py-2 text-right font-semibold">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                @endforeach
                @if($totalExpense == 0)
                    <tr><td colspan="2" class="py-2 text-gray-500 italic text-center">Belum ada pencatatan beban operasional di periode ini.</td></tr>
                @endif
                <tr class="border-t font-bold text-base">
                    <td class="py-3">Total Beban Operasional</td>
                    <td class="py-3 text-right text-orange-700">(Rp {{ number_format($totalExpense, 0, ',', '.') }})</td>
                </tr>
            </table>
        </div>

        <div class="p-5 rounded-lg flex justify-between items-center {{ $netProfit >= 0 ? 'bg-green-100 border-green-300' : 'bg-red-100 border-red-300' }} border-2 shadow-sm mt-8">
            <h3 class="font-bold text-xl uppercase tracking-wider {{ $netProfit >= 0 ? 'text-green-900' : 'text-red-900' }}">
                {{ $netProfit >= 0 ? 'Laba Bersih (Net Profit)' : 'Rugi Bersih (Net Loss)' }}
            </h3>
            <span class="font-black text-2xl {{ $netProfit >= 0 ? 'text-green-800' : 'text-red-800' }}">
                Rp {{ number_format($netProfit, 0, ',', '.') }}
            </span>
        </div>
        
        <div class="text-right mt-4">
            <button onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 shadow-sm">
                <i class="fa-solid fa-print mr-2"></i> Cetak Laporan
            </button>
        </div>
    </div>
</div>

<style>
    input[type="date"]::-webkit-calendar-picker-indicator {
        display: block !important;
        opacity: 1 !important;
        cursor: pointer;
    }
    
    @media print {
        body * { visibility: hidden; }
        .max-w-4xl, .max-w-4xl * { visibility: visible; }
        .max-w-4xl { position: absolute; left: 0; top: 0; width: 100%; padding: 0; box-shadow: none; }
        input[type="date"], button { display: none !important; }
    }
</style>