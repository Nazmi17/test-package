<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Sales\Models\Sale;
use SynApps\Modules\Inventory\Models\Category;

new class extends Component {
    use WithPagination;
    
    public $search = '';
    public $category_filter = '';

    public function updatedSearch() { $this->resetPage(); }
    public function updatedCategoryFilter() { $this->resetPage(); }

    public function with() {
        $query = Sale::latest();
        
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%');
            });
        }

        // Filter: Tampilkan nota penjualan yang mengandung produk dari kategori X
        if (!empty($this->category_filter)) {
            // Asumsi: Sale punya relasi 'details' yang mengarah ke 'product'
            $query->whereHas('details.product', function($q) {
                $q->where('category_id', $this->category_filter);
            });
        }

        return [
            'sales' => $query->paginate(10),
            'categories' => Category::orderBy('name')->get()
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold">Data Penjualan</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola transaksi penjualan barang jadi ke pelanggan.</p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto" x-data="{ openFilter: false }">
            
            <div class="relative flex-1 md:w-72">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Faktur atau Pelanggan..." class="w-full border pl-10 pr-3 py-2 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-green-200 outline-none transition-all text-sm">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>

            <div class="relative" @click.outside="openFilter = false">
                <button @click="openFilter = !openFilter" class="flex items-center justify-center border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 rounded-lg h-9 w-10 shadow-sm transition-colors relative">
                    <i class="fa-solid fa-filter text-sm"></i>
                    @if(!empty($category_filter))
                        <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-600 border-2 border-white"></span>
                        </span>
                    @endif
                </button>

                <div x-show="openFilter" x-transition x-cloak style="display: none;" class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-4">
                    <div class="flex justify-between items-center mb-3 border-b pb-2">
                        <h4 class="text-sm font-bold text-gray-800">Filter Penjualan</h4>
                        @if(!empty($category_filter))
                            <button wire:click="$set('category_filter', '')" class="text-[10px] text-red-500 hover:underline font-bold">Reset</button>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Kategori Produk</label>
                            <select wire:model.live="category_filter" class="w-full border px-3 py-2 rounded-lg bg-gray-50 text-sm focus:ring-2 focus:ring-green-200 outline-none">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <a :href="withBack('{{ route('backend.sales.sales.create') }}')" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 whitespace-nowrap text-sm font-bold flex items-center h-9">
                + Transaksi Baru
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">No. Faktur</th>
                    <th class="p-3 text-left">Pelanggan</th>
                    <th class="p-3 text-right">Total (Rp)</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ \Carbon\Carbon::parse($item->sale_date)->format('d/m/Y') }}</td>
                    <td class="p-3 font-mono text-sm font-bold">{{ $item->invoice_number }}</td>
                    <td class="p-3">{{ $item->customer_name }}</td>
                    <td class="p-3 text-right font-bold text-green-700">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                    <td class="p-3 text-center">
                        <span class="px-2 py-1 bg-gray-200 text-gray-800 text-xs font-bold rounded uppercase">{{ $item->status }}</span>
                    </td>
                    <td class="p-3 text-center">
                        <a :href="withBack('{{ route('backend.sales.sales.edit', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-6 text-center text-gray-500">Data tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sales->links() }}</div>
</div>