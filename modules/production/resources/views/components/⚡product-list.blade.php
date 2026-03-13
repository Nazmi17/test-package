<?php

use Livewire\Component;
use Livewire\WithPagination;
use SynApps\Modules\Production\Models\Product;
use SynApps\Modules\Inventory\Models\Category;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $category_filter = '';

    public function updatedSearch() { $this->resetPage(); }
    public function updatedCategoryFilter() { $this->resetPage(); }

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
        $query = Product::with('category')->latest();

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('sku', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->category_filter)) {
            $query->where('category_id', $this->category_filter);
        }

        return [
            'products'   => $query->paginate(10),
            'categories' => Category::orderBy('name')->get(),
        ];
    }   
};
?>

<div class="p-6 bg-white rounded-lg shadow-sm">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold">Data Produk & HPP</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola data produk dan kalkulasi harga pokok produksi.</p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto" x-data="{ openFilter: false }">

            <div class="relative flex-1 md:w-72">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari SKU atau nama produk..." class="w-full border pl-10 pr-3 py-2 rounded-lg bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-200 outline-none transition-all text-sm">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>

            <div class="relative" @click.outside="openFilter = false">
                <button @click="openFilter = !openFilter" class="flex items-center justify-center border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 rounded-lg h-9 w-10 shadow-sm transition-colors relative">
                    <i class="fa-solid fa-filter text-sm"></i>
                    @if(!empty($category_filter))
                        <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-600 border-2 border-white"></span>
                        </span>
                    @endif
                </button>

                <div x-show="openFilter" x-transition x-cloak style="display: none;" class="absolute right-0 mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-4">
                    <div class="flex justify-between items-center mb-3 border-b pb-2">
                        <h4 class="text-sm font-bold text-gray-800">Filter Data</h4>
                        @if(!empty($category_filter))
                            <button wire:click="$set('category_filter', '')" class="text-[10px] text-red-500 hover:underline font-bold">Reset</button>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Kategori Produk</label>
                            <select wire:model.live="category_filter" class="w-full border px-3 py-2 rounded-lg bg-gray-50 text-sm focus:ring-2 focus:ring-blue-200 outline-none">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <a :href="withBack('{{ route('backend.production.products.create') }}')" wire:navigate class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 whitespace-nowrap text-sm font-bold flex items-center h-9">
                + Buat Produk Baru
            </a>
        </div>
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
                    <th class="p-3 text-left">Kategori</th>
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
                    <td class="p-3 text-left">{{ $item->category->name ?? '-' }}</td>
                    <td class="p-3 text-right text-red-600">Rp {{ number_format($item->total_hpp, 0, ',', '.') }}</td>
                    <td class="p-3 text-right text-green-600 font-bold">Rp {{ number_format($item->suggested_price, 0, ',', '.') }}</td>
                    <td class="p-3 flex justify-center gap-3">
                        <a :href="withBack('{{ route('backend.production.products.edit', $item->id) }}')" wire:navigate class="text-blue-500 hover:underline">Edit Resep</a>
                        <button wire:confirm="Yakin hapus produk ini?" wire:click="delete({{ $item->id }}, '{{ $item->delete_token }}')" class="text-red-500 hover:underline">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-gray-500">Belum ada data produk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</div>