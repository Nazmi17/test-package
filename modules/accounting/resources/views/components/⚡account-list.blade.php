<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Accounting\Models\Account;

new class extends Component {
    use WithPagination;

    public function with()
    {
        return [
            // Urutkan berdasarkan kode akun agar rapi
            'accounts' => Account::orderBy('account_code', 'asc')->paginate(20)
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="mb-6">
        <h2 class="text-2xl font-bold">Daftar Akun (Chart of Accounts)</h2>
        <p class="text-gray-500 text-sm mt-1">Daftar akun master yang digunakan untuk penjurnalan otomatis. Akun berlabel "Sistem" tidak dapat diubah/dihapus.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="p-3 text-left">Kode Akun</th>
                    <th class="p-3 text-left">Nama Akun</th>
                    <th class="p-3 text-left">Tipe</th>
                    <th class="p-3 text-center">Status</th>
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
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-gray-500">Belum ada data akun. Silakan jalankan seeder.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $accounts->links() }}</div>
</div>