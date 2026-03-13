<?php

use Livewire\Component;
use SynApps\Modules\Inventory\Models\Category;
use VmEngine\Synapse\Traits\WithReturnUrl;

new class extends Component {
    use WithReturnUrl;

    public ?Category $category = null;
    public $name, $description;

    public function mount($id = null)
    {
        if ($id) {
            $this->category = Category::findOrFail($id);
            $this->name = $this->category->name;
            $this->description = $this->category->description;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . ($this->category->id ?? 'NULL'),
            'description' => 'nullable|string',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
        ];

        if ($this->category) {
            $this->category->update($data);
            session()->flash('success', 'Kategori berhasil diupdate!');
        } else {
            Category::create($data);
            session()->flash('success', 'Kategori baru berhasil ditambahkan!');
        }

        $this->redirectBack('backend.inventory.categories.index');
    }
};
?>

<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">{{ $category ? 'Edit' : 'Tambah' }} Kategori</h2>

    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-gray-700 font-bold mb-1 text-sm">Nama Kategori</label>
            <input wire:model="name" type="text" class="w-full border px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-200 outline-none" placeholder="Contoh: Pakaian Pria, Furnitur, dsb" required>
            @error('name') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-gray-700 font-bold mb-1 text-sm">Deskripsi (Opsional)</label>
            <textarea wire:model="description" class="w-full border px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-200 outline-none" rows="3" placeholder="Tambahkan keterangan singkat jika perlu..."></textarea>
            @error('description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
        </div>

        <div class="flex gap-3 pt-6 border-t mt-6">
            <button type="submit" class="bg-blue-600 text-white px-8 py-2.5 rounded-lg hover:bg-blue-700 font-bold shadow-md transition-colors">Simpan Kategori</button>
            <a href="{{ $backUrl ?: route('backend.inventory.categories.index') }}" wire:navigate class="bg-gray-200 text-gray-800 px-8 py-2.5 rounded-lg hover:bg-gray-300 font-bold transition-colors">Batal</a>
        </div>
    </form>
</div>