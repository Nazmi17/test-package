<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Accounting\Models\Account;
use SynApps\Modules\Accounting\Models\JournalDetail;
use VmEngine\Synapse\Traits\WithReturnUrl;
use VmEngine\Synapse\Services\Excel\ExcelExporter;
use VmEngine\Synapse\Services\Excel\Transformers\DateFormatTransformer;

new class extends Component {
    use WithPagination;
    use WithReturnUrl;

    public Account $account;
    public $startDate;
    public $endDate;

    public function mount($id)
    {
        $this->account = Account::findOrFail($id);
        $this->startDate = date('Y-m-01');
        $this->endDate = date('Y-m-t');
    }

    public function with()
    {
        $mutations = JournalDetail::with('journal')
            ->where('account_id', $this->account->id)
            ->whereHas('journal', function($q) {
                $q->whereBetween('transaction_date', [$this->startDate, $this->endDate]);
            })
            ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
            ->orderBy('journals.transaction_date', 'asc')
            ->orderBy('journals.id', 'asc')
            ->select('journal_details.*')
            ->paginate(20);

        $allTimeDetails = JournalDetail::where('account_id', $this->account->id)
            ->whereHas('journal', function($q) {
                $q->where('transaction_date', '<=', $this->endDate);
            })->get();

        $totalDebit = $allTimeDetails->sum('debit');
        $totalCredit = $allTimeDetails->sum('credit');

        if (in_array($this->account->account_type, ['Asset', 'Expense'])) {
            $endingBalance = $totalDebit - $totalCredit; 
        } else {
            $endingBalance = $totalCredit - $totalDebit; 
        }

        return compact('mutations', 'endingBalance');
    }

    public function exportExcel()
    {
        $query = JournalDetail::with('journal')
            ->where('account_id', $this->account->id)
            ->whereHas('journal', function($q) {
                $q->whereBetween('transaction_date', [$this->startDate, $this->endDate]);
            })
            ->join('journals', 'journal_details.journal_id', '=', 'journals.id')
            ->orderBy('journals.transaction_date', 'asc')
            ->orderBy('journals.id', 'asc')
            ->select('journal_details.*');

        return ExcelExporter::make($query)
            ->columns([
                'Tanggal' => [
                    'field' => 'journal.transaction_date',
                    'transformer' => DateFormatTransformer::class,
                    'config' => ['format' => 'd/m/Y'] // Format tanggal otomatis dengan transformer
                ],
                'No. Jurnal' => 'journal.journal_number',
                'Keterangan' => 'journal.description',
                'Debit' => 'debit',
                'Kredit' => 'credit',
            ])
            ->headerStyle([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '047857']], // Hijau Tua
                'alignment' => ['horizontal' => 'center'],
            ])
            ->columnStyles([
                'Tanggal' => ['width' => 15, 'alignment' => ['horizontal' => 'center']],
                'No. Jurnal' => ['width' => 20],
                'Keterangan' => ['width' => 45],
                'Debit' => ['width' => 18, 'numberFormat' => ['formatCode' => '#,##0']],
                'Kredit' => ['width' => 18, 'numberFormat' => ['formatCode' => '#,##0']],
            ])
            ->download('Buku_Besar_' . str_replace('-', '_', $this->account->account_code) . '.xlsx');
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-start mb-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ $backUrl ?: route('backend.accounting.accounts.index') }}" wire:navigate class="text-gray-500 hover:text-gray-800">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h2 class="text-2xl font-bold">Buku Besar (General Ledger)</h2>
            </div>
            <p class="text-gray-600 text-sm ml-7">
                Akun: <span class="font-mono font-bold text-blue-700">{{ $account->account_code }}</span> - <span class="font-bold">{{ $account->account_name }}</span> 
                <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded uppercase">{{ $account->account_type }}</span>
            </p>
        </div>

        <div class="flex items-end gap-3">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Dari Tanggal</label>
                <input wire:model.live="startDate" type="date" class="border px-3 py-2 rounded-lg text-sm bg-gray-50">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Sampai Tanggal</label>
                <input wire:model.live="endDate" type="date" class="border px-3 py-2 rounded-lg text-sm bg-gray-50">
            </div>
            <button wire:click="exportExcel" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-bold shadow flex items-center gap-2 mb-0.5 transition-colors h-10">
                <i class="fa-solid fa-file-excel"></i> Export
            </button>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex justify-between items-center">
        <div>
            <p class="text-xs font-bold text-blue-800 uppercase tracking-wider">Saldo Akhir Akun per {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            <p class="text-[10px] text-blue-600 mt-0.5">*Berdasarkan saldo normal ({{ in_array($account->account_type, ['Asset', 'Expense']) ? 'Debit' : 'Kredit' }})</p>
        </div>
        <div class="text-2xl font-black text-blue-900">
            Rp {{ number_format($endingBalance, 0, ',', '.') }}
        </div>
    </div>

    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">No. Jurnal</th>
                    <th class="p-3 text-left">Keterangan</th>
                    <th class="p-3 text-right">Debit (Rp)</th>
                    <th class="p-3 text-right">Kredit (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mutations as $item)
                <tr class="border-b hover:bg-gray-50 text-sm">
                    <td class="p-3 text-gray-600">{{ \Carbon\Carbon::parse($item->journal->transaction_date)->format('d/m/Y') }}</td>
                    <td class="p-3">
                        <a :href="withBack('{{ route('backend.accounting.journals.detail', $item->journal->id) }}')" wire:navigate class="font-mono font-bold text-blue-600 hover:underline">
                            {{ $item->journal->journal_number }}
                        </a>
                    </td>
                    <td class="p-3 text-gray-800">{{ $item->journal->description }}</td>
                    <td class="p-3 text-right font-semibold text-green-700">{{ $item->debit > 0 ? number_format($item->debit, 0, ',', '.') : '-' }}</td>
                    <td class="p-3 text-right font-semibold text-red-700">{{ $item->credit > 0 ? number_format($item->credit, 0, ',', '.') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500">Tidak ada mutasi/transaksi pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $mutations->links() }}</div>
</div>

<style>
    input[type="date"]::-webkit-calendar-picker-indicator { display: block !important; opacity: 1 !important; cursor: pointer; }
</style>