<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Inventory\Models\Material;

new class extends Component {
    use WithPagination;

    public function delete($id, $token)
    {
        $validId = Material::validateDeleteToken($token);
        
        if ($validId === false || $validId != $id) {
            session()->flash('error', 'Token hapus tidak valid!');
            return;
        }

        Material::findOrFail($id)->delete();
        session()->flash('success', 'Bahan baku berhasil dihapus!');
    }

    // Di SFC, kita gunakan method with() untuk mengirim data ke view di bawahnya
    public function with()
    {
        return [
            'materials' => Material::latest()->paginate(10)
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Master Bahan Baku</h2>
        <a :href="withBack('{{ route('backend.inventory.materials.create') }}')" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Tambah Bahan
        </a>
    </div>

  @if(session('success'))
        <x-synapse-alert type="success" :message="session('success')" />
    @endif

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="p-3 text-left">Nama Bahan</th>
                    <th class="p-3 text-left">Kode Bahan</th>
                    <th class="p-3 text-center">Stok</th>
                    <th class="p-3 text-left">Satuan</th>
                    <th class="p-3 text-right">Harga/Satuan</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-mono font-bold text-sm">{{ $item->code }}</td>
                    <td class="p-3">{{ $item->name }}</td>
                    <td class="p-3 text-center font-bold text-blue-600">{{ $item->stock }}</td>
                    <td class="p-3 text-right">Rp {{ number_format($item->cost_per_unit, 0, ',', '.') }}</td>
                    <td class="p-3 flex justify-center gap-3">
                        <a :href="withBack('{{ route('backend.inventory.materials.edit', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline">Edit</a>
                        <button wire:confirm="Yakin hapus bahan ini?" wire:click="delete({{ $item->id }}, '{{ $item->delete_token }}')" class="text-red-500 hover:underline">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-gray-500">Belum ada data bahan baku.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $materials->links() }}
    </div>
</div>