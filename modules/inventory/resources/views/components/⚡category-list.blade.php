<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Inventory\Models\Category;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function updatingSearch() { $this->resetPage(); }

    public function with()
    {
        $query = Category::withCount(['products', 'materials'])->latest();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        return [
            'categories' => $query->paginate(10)
        ];
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        
        // Proteksi: Jangan hapus jika masih dipakai oleh produk/bahan
        if ($category->products()->count() > 0 || $category->materials()->count() > 0) {
            session()->flash('error', 'Gagal: Kategori masih digunakan oleh produk atau bahan baku!');
            return;
        }

        $category->delete();
        session()->flash('success', 'Kategori berhasil dihapus!');
    }
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold">Kategori Barang</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola master kategori untuk produk dan bahan baku.</p>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:w-64">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kategori..." class="w-full border pl-10 pr-3 py-2 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-200 outline-none transition-all">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
            
            <a :href="withBack('{{ route('backend.inventory.categories.create') }}')" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-bold shadow whitespace-nowrap">
                + Tambah Kategori
            </a>
        </div>
    </div>

    @if(session('success'))
        <x-synapse-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-synapse-alert type="error" :message="session('error')" />
    @endif

    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="p-3 text-left">Nama Kategori</th>
                    <th class="p-3 text-left">Deskripsi</th>
                    <th class="p-3 text-center">Jml Produk</th>
                    <th class="p-3 text-center">Jml Bahan</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $item)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-bold text-gray-800">{{ $item->name }}</td>
                    <td class="p-3 text-sm text-gray-600">{{ $item->description ?: '-' }}</td>
                    <td class="p-3 text-center font-bold text-purple-600">{{ $item->products_count }}</td>
                    <td class="p-3 text-center font-bold text-indigo-600">{{ $item->materials_count }}</td>
                    <td class="p-3 flex justify-center gap-3">
                        <a :href="withBack('{{ route('backend.inventory.categories.edit', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline text-sm font-bold">Edit</a>
                        <button wire:confirm="Yakin ingin menghapus kategori ini?" wire:click="delete({{ $item->id }})" class="text-red-500 hover:underline text-sm font-bold">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-500">Data kategori tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $categories->links() }}</div>
</div>