<?php

use Livewire\Component;
use SynApps\Modules\Costing\Models\LaborService;
use VmEngine\Synapse\Traits\WithReturnUrl;

new class extends Component {
    use WithReturnUrl;

    public ?LaborService $labor = null;
    public $name, $payment_type = 'borongan', $default_cost;

    public function mount($id = null)
    {
        if ($id) {
            $this->labor = LaborService::findOrFail($id);
            $this->name = $this->labor->name;
            $this->payment_type = $this->labor->payment_type;
            $this->default_cost = $this->labor->default_cost;
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'payment_type' => 'required|in:borongan,harian',
            'default_cost' => 'required|numeric|min:0',
        ]);

        $data = [
            'name' => $this->name,
            'payment_type' => $this->payment_type,
            'default_cost' => $this->default_cost,
        ];

        if ($this->labor) {
            $this->labor->update($data);
            session()->flash('success', 'Data diupdate!');
        } else {
            LaborService::create($data);
            session()->flash('success', 'Data ditambahkan!');
        }

        $this->redirectBack('backend.costing.labors.index');
    }
};
?>

<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-6">{{ $labor ? 'Edit' : 'Tambah' }} Tenaga Kerja</h2>

    <form wire:submit="save" class="space-y-4">
        <div>
            <label class="block text-gray-700 font-medium mb-1">Nama Jasa</label>
            <input wire:model="name" type="text" class="w-full border px-4 py-2 rounded-lg" placeholder="Contoh: Jahit Kemeja" required>
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-medium mb-1">Tipe Pembayaran</label>
                <select wire:model="payment_type" class="w-full border px-4 py-2 rounded-lg">
                    <option value="borongan">Borongan (Per Pcs)</option>
                    <option value="harian">Harian</option>
                </select>
                @error('payment_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Tarif Dasar (Rp)</label>
                <input wire:model="default_cost" type="number" class="w-full border px-4 py-2 rounded-lg" placeholder="Contoh: 15000" required>
                @error('default_cost') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Simpan Data</button>
            <a href="{{ $backUrl ?: route('backend.costing.labors.index') }}" wire:navigate class="bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300">Batal</a>
        </div>
    </form>
</div>