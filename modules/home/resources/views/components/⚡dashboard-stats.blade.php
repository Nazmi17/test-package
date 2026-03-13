<?php

use Livewire\Component;
use SynApps\Modules\Sales\Models\Sale;
use SynApps\Modules\Production\Models\Manufacture;
use SynApps\Modules\Inventory\Models\Material;
use SynApps\Modules\Production\Models\Product;
use SynApps\Modules\Accounting\Models\JournalDetail;
use SynApps\Modules\Accounting\Models\Account;

new class extends Component {
    public $startDate;
    public $endDate;

    public function mount()
    {
        // Set filter ke bulan ini secara default
        $this->startDate = date('Y-m-01');
        $this->endDate = date('Y-m-t');
    }

    public function with()
    {
        // 1. HITUNG KEUANGAN (Pendapatan & Laba Bersih)
        $getBalance = function($accountId, $normalBalance = 'debit') {
            $details = JournalDetail::where('account_id', $accountId)
                ->whereHas('journal', function($q) {
                    $q->whereBetween('transaction_date', [$this->startDate, $this->endDate]);
                })->get();

            $debit = $details->sum('debit');
            $credit = $details->sum('credit');

            return $normalBalance === 'debit' ? ($debit - $credit) : ($credit - $debit);
        };

        // Revenue (Pendapatan)
        $revenueIds = Account::where('account_type', 'Revenue')->pluck('id');
        $totalRevenue = 0;
        foreach ($revenueIds as $id) $totalRevenue += $getBalance($id, 'credit');

        // HPP (COGS)
        $cogsId = Account::where('account_code', '5-5001')->value('id');
        $totalCogs = $cogsId ? $getBalance($cogsId, 'debit') : 0;

        // Expenses (Beban Operasional)
        $expenseIds = Account::where('account_type', 'Expense')->where('account_code', '!=', '5-5001')->pluck('id');
        $totalExpense = 0;
        foreach ($expenseIds as $id) $totalExpense += $getBalance($id, 'debit');

        $netProfit = $totalRevenue - $totalCogs - $totalExpense;

        // 2. HITUNG AKTIVITAS
        $totalSalesCount = Sale::whereBetween('sale_date', [$this->startDate, $this->endDate])->count();
        $totalProductionCount = Manufacture::whereBetween('production_date', [$this->startDate, $this->endDate])->count();

        // 3. PERINGATAN STOK MENIPIS
        // Bahan baku di bawah 15 yard/meter
        $lowMaterials = Material::where('stock', '<=', 15)->orderBy('stock', 'asc')->get();
        // Barang jadi di bawah 10 pcs
        $lowProducts = Product::where('stock', '<=', 10)->orderBy('stock', 'asc')->get();

        // 4. AKTIVITAS TERBARU
        $recentSales = Sale::latest()->take(5)->get();
        $recentProductions = Manufacture::with('product')->latest()->take(5)->get();

        return compact(
            'totalRevenue', 'netProfit', 'totalSalesCount', 'totalProductionCount',
            'lowMaterials', 'lowProducts', 'recentSales', 'recentProductions'
        );
    }
};
?>

<div>
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Overview Dashboard</h2>
            <p class="text-gray-500 mt-1">Pantauan aktivitas manufaktur dan keuangan terkini.</p>
        </div>
        <div class="flex gap-2">
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase">Periode Dari</label>
                <input wire:model.live="startDate" type="date" class="border px-3 py-2 rounded-lg text-sm bg-white shadow-sm">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase">Sampai</label>
                <input wire:model.live="endDate" type="date" class="border px-3 py-2 rounded-lg text-sm bg-white shadow-sm">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Pendapatan</p>
                <p class="text-xl font-black text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full {{ $netProfit >= 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center text-xl">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Laba Bersih</p>
                <p class="text-xl font-black {{ $netProfit >= 0 ? 'text-green-700' : 'text-red-700' }}">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xl">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Trx Penjualan</p>
                <p class="text-xl font-black text-gray-800">{{ $totalSalesCount }} <span class="text-sm font-normal text-gray-500">Nota</span></p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-xl">
                <i class="fa-solid fa-scissors"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Trx Produksi</p>
                <p class="text-xl font-black text-gray-800">{{ $totalProductionCount }} <span class="text-sm font-normal text-gray-500">Kali</span></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden">
            <div class="bg-red-50 px-5 py-3 border-b border-red-100 flex justify-between items-center">
                <h3 class="font-bold text-red-800"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Bahan Baku Menipis</h3>
                <span class="text-xs font-bold bg-red-200 text-red-800 px-2 py-1 rounded-full">{{ count($lowMaterials) }} item</span>
            </div>
            <div class="p-0">
                <table class="w-full text-sm">
                    @forelse($lowMaterials as $m)
                    <tr class="border-b last:border-0 hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold">{{ $m->code }} - {{ $m->name }}</td>
                        <td class="px-5 py-3 text-right">
                            <span class="font-black {{ $m->stock <= 0 ? 'text-red-600' : 'text-orange-600' }}">{{ $m->stock }} {{ $m->unit }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="px-5 py-6 text-center text-gray-500">Stok bahan baku aman.</td></tr>
                    @endforelse
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-orange-100 overflow-hidden">
            <div class="bg-orange-50 px-5 py-3 border-b border-orange-100 flex justify-between items-center">
                <h3 class="font-bold text-orange-800"><i class="fa-solid fa-boxes-stacked mr-2"></i>Barang Jadi Menipis</h3>
                <span class="text-xs font-bold bg-orange-200 text-orange-800 px-2 py-1 rounded-full">{{ count($lowProducts) }} item</span>
            </div>
            <div class="p-0">
                <table class="w-full text-sm">
                    @forelse($lowProducts as $p)
                    <tr class="border-b last:border-0 hover:bg-gray-50">
                        <td class="px-5 py-3 font-semibold">{{ $p->sku }} - {{ $p->name }}</td>
                        <td class="px-5 py-3 text-right">
                            <span class="font-black {{ $p->stock <= 0 ? 'text-red-600' : 'text-orange-600' }}">{{ $p->stock }} pcs</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="px-5 py-6 text-center text-gray-500">Stok barang jadi aman.</td></tr>
                    @endforelse
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b">Penjualan Terbaru</h3>
            <div class="space-y-3">
                @forelse($recentSales as $sale)
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-bold text-sm text-gray-800">{{ $sale->customer_name }}</p>
                        <p class="text-xs text-gray-500">{{ $sale->invoice_number }} &bull; {{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-sm text-green-600">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $sale->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700' }} uppercase">{{ $sale->status }}</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 text-sm py-4">Belum ada transaksi.</p>
                @endforelse
            </div>
            <a href="{{ route('backend.sales.sales.index') }}" wire:navigate class="block text-center text-sm font-bold text-blue-600 mt-4 pt-3 border-t hover:underline">Lihat Semua Penjualan &rarr;</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b">Produksi Terbaru</h3>
            <div class="space-y-3">
                @forelse($recentProductions as $prod)
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-bold text-sm text-gray-800">{{ $prod->product->name ?? 'Produk Dihapus' }}</p>
                        <p class="text-xs text-gray-500">{{ $prod->manufacture_number }} &bull; {{ \Carbon\Carbon::parse($prod->production_date)->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-sm text-blue-700">{{ $prod->qty }} pcs</p>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $prod->status == 'done' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800' }} uppercase">{{ str_replace('_', ' ', $prod->status) }}</span>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 text-sm py-4">Belum ada aktivitas.</p>
                @endforelse
            </div>
            <a href="{{ route('backend.production.manufactures.index') }}" wire:navigate class="block text-center text-sm font-bold text-blue-600 mt-4 pt-3 border-t hover:underline">Lihat Semua Produksi &rarr;</a>
        </div>
    </div>
</div>

<style>
    input[type="date"]::-webkit-calendar-picker-indicator { display: block !important; opacity: 1 !important; cursor: pointer; }
</style>