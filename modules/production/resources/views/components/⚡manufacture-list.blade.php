<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Production\Models\Manufacture;

new class extends Component {
    use WithPagination;
    public $search = '';

    public function updatingSearch() { $this->resetPage(); }

    public function with() {
        $query = Manufacture::with('product')->latest();
        if (!empty($this->search)) {
            $query->where('manufacture_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('product', function($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
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
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-64">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. atau Produk..." class="w-full border pl-10 pr-3 py-2 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-200 outline-none">
                <div class="absolute left-3 top-2.5 text-gray-400"><i class="fa-solid fa-magnifying-glass"></i></div>
            </div>
            <a :href="withBack('{{ route('backend.production.manufactures.create') }}')" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 whitespace-nowrap">+ Mulai Produksi</a>
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