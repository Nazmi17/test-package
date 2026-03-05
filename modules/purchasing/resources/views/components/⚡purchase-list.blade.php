<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Purchasing\Models\Purchase;

new class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            'purchases' => Purchase::latest()->paginate(10)
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold">Data Pembelian Bahan Baku</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola transaksi pembelian dari supplier.</p>
        </div>
        <a :href="withBack('{{ route('backend.purchasing.purchases.create') }}')" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Catat Pembelian
        </a>
    </div>

    @if(session('success'))
        <x-synapse-alert type="success" :message="session('success')" />
    @endif

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="p-3 text-left">Tgl Beli</th>
                    <th class="p-3 text-left">No. Faktur</th>
                    <th class="p-3 text-left">Supplier</th>
                    <th class="p-3 text-right">Total (Rp)</th>
                    <th class="p-3 text-center">Pembayaran</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ \Carbon\Carbon::parse($item->purchase_date)->format('d/m/Y') }}</td>
                    <td class="p-3 font-mono text-sm font-bold">{{ $item->invoice_number }}</td>
                    <td class="p-3">{{ $item->supplier_name }}</td>
                    <td class="p-3 text-right font-semibold">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                    <td class="p-3 text-center">
                        @if($item->payment_status == 'paid')
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Lunas</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full">Hutang</span>
                        @endif
                    </td>
                    <td class="p-3 text-center">
                        @if($item->status == 'completed')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded">SELESAI</span>
                        @else
                            <span class="px-2 py-1 bg-gray-200 text-gray-800 text-xs font-bold rounded">DRAFT</span>
                        @endif
                    </td>
                    <td class="p-3 text-center">
                        <a :href="withBack('{{ route('backend.purchasing.purchases.edit', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline">Detail/Edit</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">Belum ada transaksi pembelian.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $purchases->links() }}</div>
</div>