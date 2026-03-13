<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Accounting\Models\Journal;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function with()
    {
        $journals = Journal::with(['details.account'])
            ->where('journal_number', 'like', '%' . $this->search . '%')
            ->orWhere('description', 'like', '%' . $this->search . '%')
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(10);

        return [
            'journals' => $journals
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm border border-gray-100">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Jurnal Umum</h2>
            <p class="text-gray-500 text-sm mt-1">Daftar pencatatan transaksi akuntansi beserta detail akun debit dan kredit.</p>
        </div>
        <div class="w-full md:w-72">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Cari Jurnal</label>
            <div class="relative">
                <input wire:model.live="search" type="text" placeholder="No Jurnal / Keterangan..." class="w-full border border-gray-300 px-4 py-2 rounded-lg text-sm bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="w-full border-collapse bg-white text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs tracking-wider">
                    <th class="p-4 text-left font-semibold w-1/4">Tanggal & No Jurnal</th>
                    <th class="p-4 text-left font-semibold w-1/3">Keterangan</th>
                    <th class="p-4 text-left font-semibold">Akun Terlibat</th>
                    <th class="p-4 text-right font-semibold text-emerald-700 bg-emerald-50">Debit</th>
                    <th class="p-4 text-right font-semibold text-rose-700 bg-rose-50">Kredit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($journals as $journal)
                    @php
                        $detailCount = $journal->details->count();
                        $totalDebit = 0;
                        $totalCredit = 0;
                        
                        $refType = strtolower($journal->reference_type);
                        $refColorClass = 'bg-gray-100 text-gray-600 border-gray-200';
                        $refIcon = 'fa-tag';

                        if (str_contains($refType, 'production') || str_contains($refType, 'manufacture')) {
                            $refColorClass = 'bg-purple-50 text-purple-700 border-purple-200';
                            $refIcon = 'fa-industry';
                        } elseif (str_contains($refType, 'purchase')) {
                            $refColorClass = 'bg-blue-50 text-blue-700 border-blue-200';
                            $refIcon = 'fa-cart-shopping';
                        } elseif (str_contains($refType, 'sale')) {
                            $refColorClass = 'bg-teal-50 text-teal-700 border-teal-200';
                            $refIcon = 'fa-store';
                        } elseif (str_contains($refType, 'expense')) {
                            $refColorClass = 'bg-amber-50 text-amber-700 border-amber-200';
                            $refIcon = 'fa-file-invoice-dollar';
                        }
                    @endphp
                    
                    @foreach($journal->details as $index => $detail)
                        @php
                            $totalDebit += $detail->debit;
                            $totalCredit += $detail->credit;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors group">
                            @if($index === 0)
                                <td class="p-4 align-top border-r border-gray-100 bg-white" rowspan="{{ $detailCount }}">
                                    <div class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($journal->transaction_date)->format('d M Y') }}</div>
                                    <div class="inline-flex mt-1.5 px-2 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 rounded text-[10px] font-mono tracking-wide">
                                        {{ $journal->journal_number }}
                                    </div>
                                </td>
                                <td class="p-4 align-top border-r border-gray-100 text-gray-600 bg-white" rowspan="{{ $detailCount }}">
                                    <div class="text-sm text-gray-700">
                                        {{ $journal->description }}
                                    </div>
                                    <div class="mt-2.5 inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] uppercase font-bold border {{ $refColorClass }}">
                                        <i class="fa-solid {{ $refIcon }}"></i> {{ $journal->reference_type }}
                                    </div>
                                </td>
                            @endif
                            
                            <td class="p-3 text-gray-700">
                                <div class="font-medium">{{ $detail->account->account_code ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $detail->account->account_name ?? 'Akun Dihapus' }}</div>
                            </td>
                            <td class="p-3 text-right font-medium text-emerald-600 bg-emerald-50/30">
                                {{ $detail->debit > 0 ? 'Rp ' . number_format($detail->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="p-3 text-right font-medium text-rose-600 bg-rose-50/30">
                                {{ $detail->credit > 0 ? 'Rp ' . number_format($detail->credit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                    
                    <tr class="bg-gray-50/80 border-b-[3px] border-gray-200">
                        <td colspan="3" class="p-3 text-right text-[11px] uppercase tracking-wider text-gray-500 font-semibold">Total Balance</td>
                        <td class="p-3 text-right font-semibold text-emerald-700 bg-emerald-100/50">
                            Rp {{ number_format($totalDebit, 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-right font-semibold text-rose-700 bg-rose-100/50">
                            Rp {{ number_format($totalCredit, 0, ',', '.') }}
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <i class="fa-solid fa-file-invoice-dollar text-4xl text-gray-300"></i>
                                <p>Belum ada data riwayat jurnal umum.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4 pt-4 border-t border-gray-100">
        {{ $journals->links() }}
    </div>
</div>