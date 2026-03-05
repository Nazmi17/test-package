<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Costing\Models\LaborService;

new class extends Component {
    use WithPagination;

    public function delete($id, $token)
    {
        $validId = LaborService::validateDeleteToken($token);
        
        if ($validId === false || $validId != $id) {
            session()->flash('error', 'Token hapus tidak valid!');
            return;
        }

        LaborService::findOrFail($id)->delete();
        session()->flash('success', 'Data tenaga kerja berhasil dihapus!');
    }

    public function with()
    {
        return [
            'labors' => LaborService::latest()->paginate(10)
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Master Tenaga Kerja</h2>
        <a :href="withBack('{{ route('backend.costing.labors.create') }}')" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Tambah Tenaga Kerja
        </a>
    </div>

    @if(session('success'))
        <x-synapse-alert type="success" :message="session('success')" />
    @endif

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="p-3 text-left">Nama Jasa</th>
                    <th class="p-3 text-left">Tipe Pembayaran</th>
                    <th class="p-3 text-right">Tarif Dasar (Rp)</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($labors as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $item->name }}</td>
                    <td class="p-3"><span class="px-2 py-1 bg-gray-200 rounded text-sm capitalize">{{ $item->payment_type }}</span></td>
                    <td class="p-3 text-right">Rp {{ number_format($item->default_cost, 0, ',', '.') }}</td>
                    <td class="p-3 flex justify-center gap-3">
                        <a :href="withBack('{{ route('backend.costing.labors.edit', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline">Edit</a>
                        <button wire:confirm="Yakin hapus data ini?" wire:click="delete({{ $item->id }}, '{{ $item->delete_token }}')" class="text-red-500 hover:underline">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-gray-500">Belum ada data tenaga kerja.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $labors->links() }}</div>
</div>