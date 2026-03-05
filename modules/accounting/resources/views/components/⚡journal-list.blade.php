<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Accounting\Models\Journal;

new class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            // Load relasi details agar bisa menghitung total Debit/Kredit nanti
            'journals' => Journal::with('details')->latest()->paginate(15)
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="mb-6">
        <h2 class="text-2xl font-bold">Jurnal Transaksi (Buku Besar)</h2>
        <p class="text-gray-500 text-sm mt-1">Semua riwayat transaksi pembelian, produksi, dan penjualan akan tercatat otomatis di sini.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">No. Jurnal</th>
                    <th class="p-3 text-left">Sumber Transaksi</th>
                    <th class="p-3 text-left">Keterangan</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($journals as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}</td>
                    <td class="p-3 font-mono text-sm font-bold text-blue-600">{{ $item->journal_number }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs uppercase font-semibold">
                            {{ $item->reference_type }}
                        </span>
                    </td>
                    <td class="p-3 text-sm">{{ $item->description }}</td>
                    <td class="p-3 text-center">
                        <a :href="withBack('{{ route('backend.accounting.journals.detail', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline text-sm font-bold">Lihat Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500">Belum ada transaksi yang dijurnal.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $journals->links() }}</div>
</div>