<?php

use Livewire\Component;
use SynApps\Modules\Accounting\Models\Journal;
use VmEngine\Synapse\Traits\WithReturnUrl;

new class extends Component {
    use WithReturnUrl;

    public Journal $journal;
    public $totalDebit = 0;
    public $totalCredit = 0;

    public function mount($id)
    {
        // Load Jurnal beserta detail dan data akunnya
        $this->journal = Journal::with('details.account')->findOrFail($id);
        
        // Hitung total untuk memastikan Balance
        foreach ($this->journal->details as $detail) {
            $this->totalDebit += $detail->debit;
            $this->totalCredit += $detail->credit;
        }
    }
};
?>

<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Detail Jurnal Transaksi</h2>
        <a href="{{ $backUrl ?: route('backend.accounting.journals.index') }}" wire:navigate class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 font-bold">
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-2 gap-4 p-4 border border-gray-200 rounded-lg bg-gray-50 mb-6">
        <div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">No. Jurnal</p>
            <p class="text-lg font-mono font-bold text-blue-700">{{ $journal->journal_number }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Tanggal Transaksi</p>
            <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($journal->transaction_date)->format('d F Y') }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Sumber Transaksi</p>
            <p><span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold">{{ $journal->reference_type }}</span></p>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Keterangan</p>
            <p class="text-sm text-gray-700">{{ $journal->description }}</p>
        </div>
    </div>

    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-3 text-left">Kode Akun</th>
                    <th class="p-3 text-left">Nama Akun</th>
                    <th class="p-3 text-right">Debit (Rp)</th>
                    <th class="p-3 text-right">Kredit (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($journal->details as $detail)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-mono text-sm text-gray-600">{{ $detail->account->account_code }}</td>
                    <td class="p-3 font-semibold">{{ $detail->account->account_name }}</td>
                    <td class="p-3 text-right text-green-700 font-semibold">{{ $detail->debit > 0 ? number_format($detail->debit, 0, ',', '.') : '-' }}</td>
                    <td class="p-3 text-right text-red-700 font-semibold">{{ $detail->credit > 0 ? number_format($detail->credit, 0, ',', '.') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 border-t-2 border-gray-300">
                    <td colspan="2" class="p-4 text-right font-bold uppercase tracking-wider text-gray-600">Total Balance</td>
                    <td class="p-4 text-right font-bold text-lg text-green-800">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td class="p-4 text-right font-bold text-lg text-red-800">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    @if($totalDebit !== $totalCredit)
    <div class="mt-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm font-bold text-center border border-red-200">
        <i class="fa-solid fa-triangle-exclamation mr-1"></i> Peringatan: Jurnal tidak seimbang (Unbalanced)! Selisih: Rp {{ number_format(abs($totalDebit - $totalCredit), 0, ',', '.') }}
    </div>
    @else
    <div class="mt-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm font-bold text-center border border-green-200">
        <i class="fa-solid fa-check-circle mr-1"></i> Jurnal Seimbang (Balanced)
    </div>
    @endif
</div>