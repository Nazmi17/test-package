<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Sales\Models\Sale;

new class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            'sales' => Sale::latest()->paginate(10)
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold">Data Penjualan</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola transaksi penjualan barang jadi ke pelanggan.</p>
        </div>
        <a :href="withBack('{{ route('backend.sales.sales.create') }}')" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Transaksi Baru
        </a>
    </div>

    @if(session('success'))
        <x-synapse-alert type="success" :message="session('success')" />
    @endif

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
                        @if($item->status == 'completed')
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded">SELESAI</span>
                        @else
                            <span class="px-2 py-1 bg-gray-200 text-gray-800 text-xs font-bold rounded">DRAFT</span>
                        @endif
                    </td>
                    <td class="p-3 text-center">
                        <a :href="withBack('{{ route('backend.sales.sales.edit', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-gray-500">Belum ada transaksi penjualan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sales->links() }}</div>
</div>