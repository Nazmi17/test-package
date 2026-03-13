<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Accounting\Models\Account;
use VmEngine\Synapse\Services\Excel\ExcelExporter;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $type_filter = ''; // Properti filter baru

    // Reset halaman kalau filter/pencarian berubah
    public function updatedSearch() { $this->resetPage(); }
    public function updatedTypeFilter() { $this->resetPage(); }

    public function with()
    {
        $query = Account::orderBy('account_code', 'asc');

        // Filter Pencarian Teks
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('account_code', 'like', '%' . $this->search . '%')
                  ->orWhere('account_name', 'like', '%' . $this->search . '%');
            });
        }

        // Filter Dropdown Tipe Akun
        if (!empty($this->type_filter)) {
            $query->where('account_type', $this->type_filter);
        }

        return [
            'accounts' => $query->paginate(20),
            // Ambil daftar tipe akun yang unik langsung dari database untuk dropdown
            'accountTypes' => Account::select('account_type')->distinct()->pluck('account_type')
        ];
    }

    public function exportExcel()
    {
        $query = Account::query()->orderBy('account_code', 'asc');
        
        // Terapkan filter pencarian yang sama jika user sedang mencari sesuatu
        if (!empty($this->search)) {
            $query->where('account_code', 'like', '%' . $this->search . '%')
                  ->orWhere('account_name', 'like', '%' . $this->search . '%');
        }

        // Menggunakan Closure (fn) sangat didukung untuk Sync Export
        return ExcelExporter::make($query)
            ->columns([
                'Kode Akun' => 'account_code',
                'Nama Akun' => 'account_name',
                'Tipe Akun' => 'account_type',
                'Status' => fn($item) => $item->is_system_default ? 'Sistem' : 'Kustom'
            ])
            ->headerStyle([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D4ED8']], // Biru
                'alignment' => ['horizontal' => 'center'],
            ])
            ->columnStyles([
                'Kode Akun' => ['width' => 15, 'alignment' => ['horizontal' => 'center']],
                'Nama Akun' => ['width' => 35],
                'Tipe Akun' => ['width' => 20, 'alignment' => ['horizontal' => 'center']],
                'Status' => ['width' => 15, 'alignment' => ['horizontal' => 'center']],
            ])
            ->download('Daftar_Chart_of_Accounts.xlsx');
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold">Daftar Akun (Chart of Accounts)</h2>
            <p class="text-gray-500 text-sm mt-1">Daftar akun master yang digunakan untuk penjurnalan otomatis.</p>
        </div>
        
       <div class="flex items-center gap-3 w-full md:w-auto" x-data="{ openFilter: false }">
            
            <div class="relative flex-1 md:w-72">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kode atau nama..." class="w-full border pl-10 pr-3 py-2 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>

            <div class="relative" @click.outside="openFilter = false">
                <button @click="openFilter = !openFilter" class="flex items-center justify-center border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 rounded-lg h-9 w-10 shadow-sm transition-colors relative">
                    <i class="fa-solid fa-filter text-sm"></i>
                    @if(!empty($type_filter))
                        <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-600 border-2 border-white"></span>
                        </span>
                    @endif
                </button>

                <div x-show="openFilter" x-transition x-cloak style="display: none;" class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-4">
                    <div class="flex justify-between items-center mb-3 border-b pb-2">
                        <h4 class="text-sm font-bold text-gray-800">Filter Pencarian</h4>
                        @if(!empty($type_filter))
                            <button wire:click="$set('type_filter', '')" class="text-[10px] text-red-500 hover:underline font-bold">Reset</button>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Tipe Akun</label>
                            <select wire:model.live="type_filter" class="w-full border px-3 py-2 rounded-lg bg-gray-50 text-sm focus:ring-2 focus:ring-blue-200 outline-none">
                                <option value="">Semua Tipe Akun</option>
                                @foreach($accountTypes as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        </div>
                </div>
            </div>
            
            <button wire:click="exportExcel" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 font-bold shadow flex items-center gap-2 whitespace-nowrap text-sm h-9">
                <i class="fa-solid fa-file-excel"></i> Export
            </button>
        </div>
    </div>

    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-3 text-left">Kode Akun</th>
                    <th class="p-3 text-left">Nama Akun</th>
                    <th class="p-3 text-left">Tipe</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-mono text-sm font-bold">{{ $item->account_code }}</td>
                    <td class="p-3">{{ $item->account_name }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs uppercase font-semibold">
                            {{ $item->account_type }}
                        </span>
                    </td>
                    <td class="p-3 text-center">
                        @if($item->is_system_default)
                            <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full"><i class="fa-solid fa-lock text-xs mr-1"></i> Sistem</span>
                        @else
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Kustom</span>
                        @endif
                    </td>
                    <td class="p-3 text-center">
                        <a :href="withBack('{{ route('backend.accounting.accounts.show', $item->id) }}')" wire:navigate class="text-blue-600 hover:underline text-sm font-bold">
                            Buku Besar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-500">Data tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $accounts->links() }}</div>
</div>