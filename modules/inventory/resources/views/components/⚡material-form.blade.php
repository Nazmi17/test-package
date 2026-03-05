<?php

use Livewire\Component;
use SynApps\Modules\Inventory\Models\Material;
use VmEngine\Synapse\Traits\WithReturnUrl;

new class extends Component {
    use WithReturnUrl;

    public ?Material $material = null;
    public $code, $name, $unit = 'Meter', $cost_per_unit, $stock = 0;

    public function mount($id = null)
    {
        if ($id) {
            $this->material = Material::findOrFail($id);
            $this->code = $this->material->code;
            $this->name = $this->material->name;
            $this->unit = $this->material->unit;
            $this->cost_per_unit = $this->material->cost_per_unit;
            $this->stock = $this->material->stock;
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
            session()->flash('success', 'Bahan baku diupdate!');
        } else {
            Material::create($data);
            session()->flash('success', 'Bahan baku ditambahkan!');
        }

        $this->redirectBack('backend.inventory.materials.index');
    }
};
?>

<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-6">{{ $material ? 'Edit' : 'Tambah' }} Bahan Baku</h2>

    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">Kode Bahan</label>
                <input wire:model="code" type="text" class="w-full border px-4 py-2 rounded-lg" placeholder="Contoh: KSE.001" required>
                @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Nama Bahan</label>
                <input wire:model="name" type="text" class="w-full border px-4 py-2 rounded-lg" required>
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">Satuan</label>
                <input wire:model="unit" type="text" class="w-full border px-4 py-2 rounded-lg" placeholder="Meter/Yard/Pcs" required>
                @error('unit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Harga/Satuan (Rp)</label>
                <input wire:model="cost_per_unit" type="number" class="w-full border px-4 py-2 rounded-lg" required>
                @error('cost_per_unit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-1">Stok Awal</label>
                <input wire:model="stock" type="number" step="0.01" class="w-full border px-4 py-2 rounded-lg text-blue-700 font-bold" required>
            </div>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Simpan Data</button>
            <a href="{{ $backUrl ?: route('backend.inventory.materials.index') }}" wire:navigate class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300">Batal</a>
        </div>
    </form>
</div>