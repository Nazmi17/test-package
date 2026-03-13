<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Production\Models\Manufacture;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $date_from = '';
    public $date_to = '';

    public function updatedSearch() { $this->resetPage(); }
    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }

    public function resetFilters()
    {
        $this->date_from = '';
        $this->date_to = '';
        $this->resetPage();
    }

    public function with() {
        $query = Manufacture::with('product')->latest();

        if (!empty($this->search)) {
            $query->where('manufacture_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('product', function($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
        }

        if (!empty($this->date_from)) {
            $query->whereDate('production_date', '>=', $this->date_from);
        }

        if (!empty($this->date_to)) {
            $query->whereDate('production_date', '<=', $this->date_to);
        }

        return ['manufactures' => $query->paginate(10)];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold">Data Eksekusi Produksi</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola proses pembuatan barang jadi dari bahan baku.</p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto" x-data="{ openFilter: false }">
            <div class="relative flex-1 md:w-64">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. atau Produk..." class="w-full border pl-10 pr-3 py-2 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-200 outline-none text-sm">
                <div class="absolute left-3 top-2.5 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></div>
            </div>

            <div class="relative" @click.outside="openFilter = false">
                <button @click="openFilter = !openFilter" class="flex items-center justify-center border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 rounded-lg h-9 w-10 shadow-sm transition-colors relative">
                    <i class="fa-solid fa-filter text-sm"></i>
                    @if(!empty($date_from) || !empty($date_to))
                        <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-600 border-2 border-white"></span>
                        </span>
                    @endif
                </button>

                <div x-show="openFilter" x-transition x-cloak style="display: none;" class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-4">
                    <div class="flex justify-between items-center mb-3 border-b pb-2">
                        <h4 class="text-sm font-bold text-gray-800">Filter Data</h4>
                        @if(!empty($date_from) || !empty($date_to))
                            <button wire:click="resetFilters()" class="text-[10px] text-red-500 hover:underline font-bold">Reset</button>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Dari Tanggal Produksi</label>
                            <input wire:model.live="date_from" type="date" class="w-full border px-3 py-2 rounded-lg bg-gray-50 text-sm focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Sampai Tanggal Produksi</label>
                            <input wire:model.live="date_to" type="date" class="w-full border px-3 py-2 rounded-lg bg-gray-50 text-sm focus:ring-2 focus:ring-blue-200 outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <a :href="withBack('{{ route('backend.production.manufactures.create') }}')" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 whitespace-nowrap text-sm font-bold flex items-center h-9">+ Mulai Produksi</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="p-3 text-left">Tgl Produksi</th>
                    <th class="p-3 text-left">No. Produksi</th>
                    <th class="p-3 text-left">Barang Jadi</th>
                    <th class="p-3 text-center">QTY Dibuat</th>
                    <th class="p-3 text-right">Total Nilai HPP</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($manufactures as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ \Carbon\Carbon::parse($item->production_date)->format('d/m/Y') }}</td>
                    <td class="p-3 font-mono text-sm font-bold">{{ $item->manufacture_number }}</td>
                    <td class="p-3 font-semibold text-blue-700">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                    <td class="p-3 text-center font-bold">{{ $item->qty }} pcs</td>
                    <td class="p-3 text-right">Rp {{ number_format($item->total_hpp * $item->qty, 0, ',', '.') }}</td>
                    <td class="p-3 text-center">
                        <span class="px-2 py-1 bg-gray-200 text-gray-800 text-xs font-bold rounded uppercase">{{ $item->status }}</span>
                    </td>
                    <td class="p-3 text-center">
                        <a :href="withBack('{{ route('backend.production.manufactures.edit', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="p-6 text-center text-gray-500">Data tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $manufactures->links() }}</div>
</div>

<style>
    input[type="date"]::-webkit-calendar-picker-indicator {
        display: block !important;
        opacity: 1 !important;
        cursor: pointer;
    }
</style>