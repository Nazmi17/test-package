<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Production\Models\Manufacture;

new class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            'manufactures' => Manufacture::with('product')->latest()->paginate(10)
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold">Data Eksekusi Produksi</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola proses pembuatan barang jadi dari bahan baku.</p>
        </div>
        <a :href="withBack('{{ route('backend.production.manufactures.create') }}')" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Mulai Produksi
        </a>
    </div>

    @if(session('success'))
        <x-synapse-alert type="success" :message="session('success')" />
    @endif

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
                        @if($item->status == 'done')
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded">SELESAI</span>
                        @elseif($item->status == 'on_process')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded">PROSES</span>
                        @else
                            <span class="px-2 py-1 bg-gray-200 text-gray-800 text-xs font-bold rounded">DRAFT</span>
                        @endif
                    </td>
                    <td class="p-3 text-center">
                        <a :href="withBack('{{ route('backend.production.manufactures.edit', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-gray-500">Belum ada data produksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $manufactures->links() }}</div>
</div>