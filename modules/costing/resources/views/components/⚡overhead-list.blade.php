<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Costing\Models\Overhead;

new class extends Component {
    use WithPagination;

    public function delete($id, $token)
    {
        $validId = Overhead::validateDeleteToken($token);
        
        if ($validId === false || $validId != $id) {
            session()->flash('error', 'Token hapus tidak valid!');
            return;
        }

        Overhead::findOrFail($id)->delete();
        session()->flash('success', 'Data overhead berhasil dihapus!');
    }

    public function with()
    {
        return [
            'overheads' => Overhead::latest()->paginate(10)
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Master Biaya Overhead</h2>
        <a :href="withBack('{{ route('backend.costing.overheads.create') }}')" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Tambah Overhead
        </a>
    </div>

    @if(session('success'))
        <x-synapse-alert type="success" :message="session('success')" />
    @endif

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="p-3 text-left">Nama Biaya</th>
                    <th class="p-3 text-left">Tipe</th>
                    <th class="p-3 text-right">Nominal (Rp)</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($overheads as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $item->name }}</td>
                    <td class="p-3"><span class="px-2 py-1 bg-gray-200 rounded text-sm capitalize">{{ $item->type }}</span></td>
                    <td class="p-3 text-right">Rp {{ number_format($item->cost_amount, 0, ',', '.') }}</td>
                    <td class="p-3 flex justify-center gap-3">
                        <a :href="withBack('{{ route('backend.costing.overheads.edit', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline">Edit</a>
                        <button wire:confirm="Yakin hapus data ini?" wire:click="delete({{ $item->id }}, '{{ $item->delete_token }}')" class="text-red-500 hover:underline">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-gray-500">Belum ada data overhead.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $overheads->links() }}</div>
</div>