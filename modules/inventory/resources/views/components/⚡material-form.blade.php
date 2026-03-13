<?php

use Livewire\Component;
use SynApps\Modules\Inventory\Models\Material;
use SynApps\Modules\Inventory\Models\Category;
use VmEngine\Synapse\Traits\WithReturnUrl;

new class extends Component {
    use WithReturnUrl;

    public ?Material $material = null;
    public $code, $name, $unit = 'Meter', $cost_per_unit, $stock = 0;
    
    public $category_ids = []; 
    public $availableCategories = [];

    public function mount($id = null)
    {
        $this->availableCategories = Category::orderBy('name')->get();

        if ($id) {
            $this->material = Material::findOrFail($id);
            $this->code = $this->material->code;
            $this->name = $this->material->name;
            $this->unit = $this->material->unit;
            $this->cost_per_unit = $this->material->cost_per_unit;
            $this->stock = $this->material->stock;
            
            $this->category_ids = $this->material->categories->pluck('id')->toArray();
        }
    }

    public function save()
    {
        $this->validate([
            'code' => 'required|string|unique:materials,code,' . ($this->material->id ?? 'NULL'),
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'cost_per_unit' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'category_ids' => 'array',
        ]);

        $data = [
            'code' => $this->code,
            'name' => $this->name,
            'unit' => $this->unit,
            'cost_per_unit' => $this->cost_per_unit,
            'stock' => $this->stock,
        ];

        if ($this->material) {
            $this->material->update($data);
            $savedMaterial = $this->material;
            session()->flash('success', 'Bahan baku diupdate!');
        } else {
            $savedMaterial = Material::create($data);
            session()->flash('success', 'Bahan baku ditambahkan!');
        }

        $savedMaterial->categories()->sync($this->category_ids);

        $this->redirectBack('backend.inventory.materials.index');
    }
};
?>

{{-- SATU DIV PEMBUNGKUS UTAMA UNTUK SELURUH KOMPONEN --}}
<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-6">{{ $material ? 'Edit' : 'Tambah' }} Bahan Baku</h2>

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-bold mb-1 text-sm">Kode Bahan</label>
                <input wire:model="code" type="text" class="w-full border px-4 py-2 rounded-lg" placeholder="Contoh: MB-001" required>
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-1 text-sm">Nama Bahan</label>
                <input wire:model="name" type="text" class="w-full border px-4 py-2 rounded-lg" placeholder="Contoh: Kain Katun" required>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700 font-bold mb-1 text-sm">Satuan</label>
                <input wire:model="unit" type="text" class="w-full border px-4 py-2 rounded-lg" placeholder="Contoh: Meter, Kg, Pcs" required>
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-1 text-sm">Harga/Satuan (Rp)</label>
                <input wire:model="cost_per_unit" type="number" class="w-full border px-4 py-2 rounded-lg" placeholder="Contoh: 15000" required>
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-1 text-sm">Stok Awal</label>
                <input wire:model="stock" type="number" step="0.01" class="w-full border px-4 py-2 rounded-lg text-blue-700 font-bold" required>
            </div>
        </div>

        <div>
            <label for="select-categories" class="block text-gray-700 font-bold mb-1 text-sm">
                Kategori Bahan Baku
            </label>
            
            @if(count($availableCategories) > 0)
                {{-- wire:ignore SANGAT PENTING --}}
                <div wire:ignore>
                    <select id="select-categories" multiple placeholder="Ketik atau pilih kategori..." class="w-full border px-3 py-2 rounded-lg bg-white" autocomplete="off">
                        @foreach($availableCategories as $category)
                            <option value="{{ $category->id }}" {{ in_array($category->id, $category_ids) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div class="p-3 bg-red-50 text-red-700 border border-red-200 rounded-lg text-sm">
                    Belum ada master kategori. Silakan tambahkan di menu Kategori terlebih dahulu.
                </div>
            @endif
        </div>

        <div class="flex gap-3 pt-4 border-t">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold">Simpan Data</button>
            <a href="{{ $backUrl ?: route('backend.inventory.materials.index') }}" wire:navigate class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 font-bold">Batal</a>
        </div>
    </form>

    {{-- SEMUA SCRIPT & STYLE HARUS BERADA DI DALAM DIV UTAMA INI --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    @script
    <script>
        document.addEventListener('livewire:initialized', () => {
            let el = document.getElementById('select-categories');
            
            if (el) {
                new TomSelect(el, {
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    plugins: ['remove_button'],
                    onChange: function(values) {
                        $wire.set('category_ids', values);
                    }
                });
            }
        });
    </script>
    @endscript

    <style>
        .ts-control {
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            border-color: #e5e7eb;
        }
        .ts-control.focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 2px rgba(191, 219, 254, 0.5);
        }
    </style>
</div>