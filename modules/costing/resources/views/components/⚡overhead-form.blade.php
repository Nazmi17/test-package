<?php

use Livewire\Component;
use SynApps\Modules\Costing\Models\Overhead;
use VmEngine\Synapse\Traits\WithReturnUrl;

new class extends Component {
    use WithReturnUrl;

    public ?Overhead $overhead = null;
    public $name, $type = 'tetap', $cost_amount;

    public function mount($id = null)
    {
        if ($id) {
            $this->overhead = Overhead::findOrFail($id);
            $this->name = $this->overhead->name;
            $this->type = $this->overhead->type;
            $this->cost_amount = $this->overhead->cost_amount;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:tetap,variabel',
            'cost_amount' => 'required|numeric|min:0',
        ]);

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'cost_amount' => $this->cost_amount,
        ];

        if ($this->overhead) {
            $this->overhead->update($data);
            session()->flash('success', 'Data overhead diupdate!');
        } else {
            Overhead::create($data);
            session()->flash('success', 'Data overhead ditambahkan!');
        }

        $this->redirectBack('backend.costing.overheads.index');
    }
};
?>

<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-6">{{ $overhead ? 'Edit' : 'Tambah' }} Biaya Overhead</h2>

    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-gray-700 font-medium mb-1">Nama Biaya (Contoh: Listrik, Packaging)</label>
            <input wire:model="name" type="text" class="w-full border px-4 py-2 rounded-lg" required>
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">Tipe Overhead</label>
                <select wire:model="type" class="w-full border px-4 py-2 rounded-lg">
                    <option value="tetap">Tetap (Fixed Cost)</option>
                    <option value="variabel">Variabel (Variable Cost)</option>
                </select>
                @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Nominal (Rp)</label>
                <input wire:model="cost_amount" type="number" class="w-full border px-4 py-2 rounded-lg" required>
                @error('cost_amount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Simpan Data</button>
            <a href="{{ $backUrl ?: route('backend.costing.overheads.index') }}" wire:navigate class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300">Batal</a>
        </div>
    </form>
</div>