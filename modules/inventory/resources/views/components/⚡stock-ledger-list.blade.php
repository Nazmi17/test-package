<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Inventory\Models\StockLedger;

new class extends Component {
    use WithPagination;

    // Properti untuk filter (opsional, biar keren)
    public $filterType = ''; 

    public function with()
    {
        // Ambil data ledger beserta relasi polymorphic-nya (Material / Product)
        $query = StockLedger::with('item')->latest();

        // Logika filter sederhana
        if ($this->filterType === 'material') {
            $query->where('item_type', 'like', '%Material%');
        } elseif ($this->filterType === 'product') {
            $query->where('item_type', 'like', '%Product%');
        }

        return [
            'ledgers' => $query->paginate(15)
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-2xl font-bold">Kartu Stok (Stock Ledger)</h2>
            <p class="text-gray-500 text-sm mt-1">Riwayat pergerakan (masuk/keluar) Bahan Baku dan Barang Jadi.</p>
        </div>
        
        <div class="w-48">
            <label class="block text-xs font-bold text-gray-700 mb-1">Filter Kategori</label>
            <select wire:model.live="filterType" class="w-full border px-3 py-2 rounded-lg text-sm bg-gray-50">
                <option value="">Semua Barang</option>
                <option value="material">Hanya Bahan Baku</option>
                <option value="product">Hanya Barang Jadi</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="p-3 text-left">Waktu</th>
                    <th class="p-3 text-left">Tipe</th>
                    <th class="p-3 text-left">Nama Barang</th>
                    <th class="p-3 text-center">In/Out</th>
                    <th class="p-3 text-center">QTY</th>
                    <th class="p-3 text-center">Sisa Stok</th>
                    <th class="p-3 text-left">Sumber & Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ledgers as $item)
                <tr class="border-b hover:bg-gray-50 text-sm">
                    <td class="p-3 text-gray-600">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    <td class="p-3">
                        @if(str_contains($item->item_type, 'Material'))
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-bold">BAHAN</span>
                        @else
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-bold">PRODUK</span>
                        @endif
                    </td>
                    <td class="p-3 font-semibold text-gray-800">
                        {{ $item->item->name ?? 'Barang Dihapus' }}
                    </td>
                    <td class="p-3 text-center">
                        @if($item->transaction_type == 'in')
                            <span class="text-green-600 font-bold"><i class="fa-solid fa-arrow-down mr-1"></i> MASUK</span>
                        @else
                            <span class="text-red-600 font-bold"><i class="fa-solid fa-arrow-up mr-1"></i> KELUAR</span>
                        @endif
                    </td>
                    <td class="p-3 text-center font-bold {{ $item->transaction_type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $item->qty }}
                    </td>
                    <td class="p-3 text-center font-bold text-blue-700 bg-blue-50">
                        {{ $item->balance_after }}
                    </td>
                    <td class="p-3">
                        <span class="font-bold text-gray-700">[{{ strtoupper($item->reference_type) }}]</span> 
                        <span class="text-gray-500">{{ $item->description }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">Belum ada riwayat pergerakan stok.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $ledgers->links() }}</div>
</div>