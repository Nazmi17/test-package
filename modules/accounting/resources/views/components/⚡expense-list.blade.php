<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Accounting\Models\Expense;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function updatingSearch() { $this->resetPage(); }

    public function with()
    {
        $query = Expense::with(['expenseAccount', 'paymentAccount'])->latest();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('expense_number', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  // Mencari berdasarkan nama akun beban juga
                  ->orWhereHas('expenseAccount', function($subQuery) {
                      $subQuery->where('account_name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return [
            'expenses' => $query->paginate(10)
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold">Pengeluaran Operasional (Expenses)</h2>
            <p class="text-gray-500 text-sm mt-1">Catat biaya listrik, gaji admin, iklan, dan beban di luar produksi pabrik.</p>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-64">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari bukti atau keterangan..." class="w-full border pl-10 pr-3 py-2 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-orange-200 outline-none transition-all">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
            
            <a :href="withBack('{{ route('backend.accounting.expenses.create') }}')" wire:navigate class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 font-bold shadow whitespace-nowrap">
                + Catat Pengeluaran
            </a>
        </div>
    </div>

    @if(session('success'))
        <x-synapse-alert type="success" :message="session('success')" />
    @endif

    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">No. Bukti</th>
                    <th class="p-3 text-left">Keterangan</th>
                    <th class="p-3 text-left">Akun Beban</th>
                    <th class="p-3 text-left">Dibayar Dari</th>
                    <th class="p-3 text-right">Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $item)
                <tr class="border-b hover:bg-gray-50 text-sm">
                    <td class="p-3">{{ \Carbon\Carbon::parse($item->expense_date)->format('d/m/Y') }}</td>
                    <td class="p-3 font-mono font-bold text-orange-700">{{ $item->expense_number }}</td>
                    <td class="p-3 text-gray-700">{{ $item->description }}</td>
                    <td class="p-3 font-semibold">{{ $item->expenseAccount->account_name }}</td>
                    <td class="p-3 text-gray-600">{{ $item->paymentAccount->account_name }}</td>
                    <td class="p-3 text-right font-bold text-red-600">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-gray-500">
                        <i class="fa-solid fa-folder-open text-3xl mb-2 text-gray-300 block"></i>
                        Data pengeluaran tidak ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $expenses->links() }}</div>
</div>