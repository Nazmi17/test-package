<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Inventory\Models\StockLedger;

new class extends Component {
    use WithPagination;

    public $filterType = '';
    public $date_from = '';
    public $date_to = '';

    public function updatedFilterType() { $this->resetPage(); }
    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }

    public function resetFilters()
    {
        $this->filterType = '';
        $this->date_from = '';
        $this->date_to = '';
        $this->resetPage();
    }

    public function with()
    {
        $query = StockLedger::with('item')->latest();

        if ($this->filterType === 'material') {
            $query->where('item_type', 'like', '%Material%');
        } elseif ($this->filterType === 'product') {
            $query->where('item_type', 'like', '%Product%');
        }

        if (!empty($this->date_from)) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }

        if (!empty($this->date_to)) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        return [
            'ledgers' => $query->paginate(15)
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kartu Stok (Stock Ledger)</h2>
            <p class="text-gray-500 text-sm mt-1">Lacak histori pergerakan stok awal, mutasi, dan stok akhir barang.</p>
        </div>

        <div class="relative" x-data="{ openFilter: false }" @click.outside="openFilter = false">
            <button @click="openFilter = !openFilter" class="flex items-center justify-center border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 rounded-lg h-9 w-10 shadow-sm transition-colors relative">
                <i class="fa-solid fa-filter text-sm"></i>
                @if(!empty($filterType) || !empty($date_from) || !empty($date_to))
                    <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-600 border-2 border-white"></span>
                    </span>
                @endif
            </button>

            <div x-show="openFilter" x-transition x-cloak style="display: none;" class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-4">
                <div class="flex justify-between items-center mb-3 border-b pb-2">
                    <h4 class="text-sm font-bold text-gray-800">Filter Data</h4>
                    @if(!empty($filterType) || !empty($date_from) || !empty($date_to))
                        <button wire:click="resetFilters()" class="text-[10px] text-red-500 hover:underline font-bold">Reset</button>
                    @endif
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Jenis Barang</label>
                        <select wire:model.live="filterType" class="w-full border px-3 py-2 rounded-lg bg-gray-50 text-sm focus:ring-2 focus:ring-blue-200 outline-none">
                            <option value="">Semua Barang</option>
                            <option value="material">Hanya Bahan Baku</option>
                            <option value="product">Hanya Barang Jadi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Dari Tanggal</label>
                        <input wire:model.live="date_from" type="date" class="w-full border px-3 py-2 rounded-lg bg-gray-50 text-sm focus:ring-2 focus:ring-blue-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Sampai Tanggal</label>
                        <input wire:model.live="date_to" type="date" class="w-full border px-3 py-2 rounded-lg bg-gray-50 text-sm focus:ring-2 focus:ring-blue-200 outline-none">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full border-collapse bg-white text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs tracking-wider">
                    <th class="p-4 text-left font-semibold">Tanggal & Waktu</th>
                    <th class="p-4 text-left font-semibold">Detail Barang</th>
                    <th class="p-4 text-left font-semibold">Referensi & Keterangan</th>
                    <th class="p-4 text-center font-semibold bg-gray-100">Stok Awal</th>
                    <th class="p-4 text-center font-semibold">Mutasi (Qty)</th>
                    <th class="p-4 text-center font-semibold bg-blue-50 text-blue-800">Stok Akhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($ledgers as $item)
                    @php
                        $stokAwal = $item->transaction_type === 'in'
                            ? $item->balance_after - $item->qty
                            : $item->balance_after + $item->qty;

                        $unit = $item->item->unit ?? '';
                        $isMaterial = str_contains($item->item_type, 'Material');
                    @endphp
                <tr class="hover:bg-blue-50/50 transition-colors group">
                    <td class="p-4 text-gray-500 whitespace-nowrap">
                        <div class="font-medium text-gray-800">{{ $item->created_at->format('d M Y') }}</div>
                        <div class="text-xs">{{ $item->created_at->format('H:i') }} WIB</div>
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-2 mb-1">
                            @if($isMaterial)
                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[10px] font-bold uppercase tracking-wide">Bahan</span>
                            @else
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold uppercase tracking-wide">Produk</span>
                            @endif
                        </div>
                        <div class="font-bold text-gray-800">{{ $item->item->name ?? 'Barang Dihapus' }}</div>
                        @if($item->item && isset($item->item->sku))
                            <div class="text-xs text-gray-400 font-mono">{{ $item->item->sku }}</div>
                        @endif
                    </td>
                    <td class="p-4">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="px-2 py-0.5 bg-gray-100 border border-gray-200 text-gray-600 rounded text-[10px] font-bold uppercase">
                                {{ $item->reference_type }}
                            </span>
                        </div>
                        <div class="text-gray-600 text-xs line-clamp-2" title="{{ $item->description }}">
                            {{ $item->description }}
                        </div>
                    </td>
                    <td class="p-4 text-center font-medium text-gray-500 bg-gray-50/50 group-hover:bg-transparent">
                        {{ number_format($stokAwal, 2) }} <span class="text-xs">{{ $unit }}</span>
                    </td>
                    <td class="p-4 text-center">
                        @if($item->transaction_type == 'in')
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 font-bold border border-green-200">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>{{ number_format($item->qty, 2) }} <span class="text-[10px] font-normal">{{ $unit }}</span></span>
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-red-700 font-bold border border-red-200">
                                <i class="fa-solid fa-minus text-xs"></i>
                                <span>{{ number_format($item->qty, 2) }} <span class="text-[10px] font-normal">{{ $unit }}</span></span>
                            </div>
                        @endif
                    </td>
                    <td class="p-4 text-center font-bold text-blue-700 bg-blue-50/50 group-hover:bg-blue-100/50">
                        {{ number_format($item->balance_after, 2) }} <span class="text-xs text-blue-500 font-medium">{{ $unit }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <i class="fa-solid fa-box-open text-3xl text-gray-300"></i>
                            <p>Belum ada riwayat pergerakan stok.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 pt-4 border-t border-gray-100">
        {{ $ledgers->links() }}
    </div>
</div>

<style>
    input[type="date"]::-webkit-calendar-picker-indicator {
        display: block !important;
        opacity: 1 !important;
        cursor: pointer;
    }
</style>