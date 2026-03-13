<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Accounting\Models\Journal;
use SynApps\Modules\Accounting\Models\JournalDetail;
use VmEngine\Synapse\Services\Excel\ExcelExporter;
use VmEngine\Synapse\Services\Excel\Transformers\DateFormatTransformer;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function updatingSearch() { $this->resetPage(); }

    public function with()
    {
        $query = Journal::with('details')->latest();

        if (!empty($this->search)) {
            $query->where('journal_number', 'like', '%' . $this->search . '%')
                  ->orWhere('reference_type', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        return [
            'journals' => $query->paginate(15)
        ];
    }

    // Fungsi Magis Export Excel
    public function exportExcel()
    {
        // Ambil data detail jurnal agar format tabelnya memanjang ke bawah (Debit/Kredit)
        $query = JournalDetail::query()
            ->with(['journal', 'account'])
            ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
            ->orderBy('journals.transaction_date', 'desc')
            ->orderBy('journals.journal_number', 'desc')
            ->select('journal_details.*');

        return ExcelExporter::make($query)
            ->columns([
                'Tanggal' => [
                    'field' => 'journal.transaction_date',
                    'transformer' => DateFormatTransformer::class,
                    'config' => ['format' => 'd/m/Y']
                ],
                'No. Jurnal' => 'journal.journal_number',
                'Sumber' => 'journal.reference_type',
                'Kode Akun' => 'account.account_code',
                'Nama Akun' => 'account.account_name',
                'Keterangan' => 'journal.description',
                'Debit' => 'debit',
                'Kredit' => 'credit',
            ])
            ->headerStyle([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D4ED8']], // Biru Tailwind
                'alignment' => ['horizontal' => 'center'],
            ])
            ->columnStyles([
                'Tanggal' => ['width' => 15, 'alignment' => ['horizontal' => 'center']],
                'No. Jurnal' => ['width' => 20],
                'Sumber' => ['width' => 15, 'alignment' => ['horizontal' => 'center']],
                'Kode Akun' => ['width' => 15, 'alignment' => ['horizontal' => 'center']],
                'Nama Akun' => ['width' => 30],
                'Keterangan' => ['width' => 40],
                // Format angka di Excel otomatis pakai pemisah ribuan
                'Debit' => ['width' => 18, 'numberFormat' => ['formatCode' => '#,##0']],
                'Kredit' => ['width' => 18, 'numberFormat' => ['formatCode' => '#,##0']],
            ])
            ->download('Laporan_Jurnal_Transaksi.xlsx');
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold">Jurnal Transaksi (Buku Besar)</h2>
            <p class="text-gray-500 text-sm mt-1">Semua riwayat transaksi pembelian, produksi, dan penjualan.</p>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-64">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. Jurnal atau Keterangan..." class="w-full border pl-10 pr-3 py-2 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>

            <button wire:click="exportExcel" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-bold shadow flex items-center gap-2 whitespace-nowrap transition-colors">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>

    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
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
                    <td class="p-3">{{ \Carbon\Carbon::parse($item->transaction_date)->format('d/m/Y') }}</td>
                    <td class="p-3 font-mono text-sm font-bold text-blue-600">{{ $item->journal_number }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs uppercase font-semibold">
                            {{ $item->reference_type }}
                        </span>
                    </td>
                    <td class="p-3 text-sm text-gray-700">{{ $item->description }}</td>
                    <td class="p-3 text-center">
                        <a :href="withBack('{{ route('backend.accounting.journals.detail', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline text-sm font-bold">Lihat Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-500">
                        <i class="fa-solid fa-folder-open text-3xl mb-2 text-gray-300 block"></i>
                        Data jurnal tidak ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $journals->links() }}</div>
</div>