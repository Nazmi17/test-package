<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Production\Models\Product;

new class extends Component {
    use WithPagination;

    public function delete($id, $token)
    {
        $validId = Product::validateDeleteToken($token);
        
        if ($validId === false || $validId != $id) {
            session()->flash('error', 'Token hapus tidak valid!');
            return;
        }

        Product::findOrFail($id)->delete();
        session()->flash('success', 'Data produk berhasil dihapus!');
    }

    public function with()
    {
        return [
            'products' => Product::latest()->paginate(10)
        ];
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Data Produk & HPP</h2>
        <a :href="withBack('{{ route('backend.production.products.create') }}')" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Buat Produk Baru
        </a>
    </div>

    @if(session('success'))
        <x-synapse-alert type="success" :message="session('success')" />
    @endif

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="p-3 text-left">SKU</th>
                    <th class="p-3 text-left">Nama Produk</th>
                    <th class="p-3 text-right">Total HPP</th>
                    <th class="p-3 text-right">Saran Harga Jual</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-mono text-sm">{{ $item->sku }}</td>
                    <td class="p-3 font-semibold">{{ $item->name }}</td>
                    <td class="p-3 text-right text-red-600">Rp {{ number_format($item->total_hpp, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-green-600 font-bold">Rp {{ number_format($item->suggested_price, 0, ',', '.') }}</td>
                    <td class="p-3 flex justify-center gap-3">
                        <a :href="withBack('{{ route('backend.production.products.edit', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline">Edit Resep</a>
                        <button wire:confirm="Yakin hapus produk ini?" wire:click="delete({{ $item->id }}, '{{ $item->delete_token }}')" class="text-red-500 hover:underline">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-6 text-center text-gray-500">Belum ada data produk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</div>