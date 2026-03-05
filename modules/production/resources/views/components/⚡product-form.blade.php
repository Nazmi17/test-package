<?php

use Livewire\Component;
use SynApps\Modules\Production\Models\Product;
use SynApps\Modules\Inventory\Models\Material;
use SynApps\Modules\Costing\Models\LaborService;
use SynApps\Modules\Costing\Models\Overhead;
use VmEngine\Synapse\Traits\WithReturnUrl;

new class extends Component {
    use WithReturnUrl;

    public ?Product $product = null;
    
    // Form Master
    public $sku, $name, $markup_percentage = 30;

    // Form Dynamic Arrays
    public $selectedMaterials = [];
    public $selectedLabors = [];
    public $selectedOverheads = [];

    // Master Data untuk Dropdown
    public $masterMaterials, $masterLabors, $masterOverheads;

    public function mount($id = null)
    {
        // Load data dropdown
        $this->masterMaterials = Material::all();
        $this->masterLabors = LaborService::all();
        $this->masterOverheads = Overhead::all();

        if ($id) {
            // Mode EDIT
            $this->product = Product::with(['materials', 'labors', 'overheads'])->findOrFail($id);
            $this->sku = $this->product->sku;
            $this->name = $this->product->name;
            $this->markup_percentage = $this->product->markup_percentage;

            // Load data pivot ke array form
            foreach ($this->product->materials as $m) {
                $this->selectedMaterials[] = ['id' => $m->id, 'qty' => $m->pivot->quantity_required, 'waste' => $m->pivot->waste_percentage];
            }
            foreach ($this->product->labors as $l) {
                $this->selectedLabors[] = ['id' => $l->id, 'cost' => $l->pivot->cost];
            }
            foreach ($this->product->overheads as $o) {
                $this->selectedOverheads[] = ['id' => $o->id, 'cost' => $o->pivot->allocated_cost];
            }
        } else {
            // Mode CREATE: Kasih 1 baris kosong default
            $this->addMaterial();
            $this->addLabor();
            $this->addOverhead();
        }
    }

    // --- FUNGSI ADD & REMOVE ROW ---
    public function addMaterial() { $this->selectedMaterials[] = ['id' => '', 'qty' => 0, 'waste' => 0]; }
    public function removeMaterial($index) { unset($this->selectedMaterials[$index]); $this->selectedMaterials = array_values($this->selectedMaterials); }

    public function addLabor() { $this->selectedLabors[] = ['id' => '', 'cost' => 0]; }
    public function removeLabor($index) { unset($this->selectedLabors[$index]); $this->selectedLabors = array_values($this->selectedLabors); }

    public function addOverhead() { $this->selectedOverheads[] = ['id' => '', 'cost' => 0]; }
    public function removeOverhead($index) { unset($this->selectedOverheads[$index]); $this->selectedOverheads = array_values($this->selectedOverheads); }

    // --- FUNGSI SAVE ---
    public function save()
    {
        $this->validate([
            'sku' => 'required|string|unique:products,sku,' . ($this->product->id ?? 'NULL'),
            'name' => 'required|string',
            'markup_percentage' => 'required|numeric|min:0',
        ]);

        // 1. Simpan Data Master Produk
        $product = Product::updateOrCreate(
            ['id' => $this->product->id ?? null],
            ['sku' => $this->sku, 'name' => $this->name, 'markup_percentage' => $this->markup_percentage]
        );

        // 2. Format & Sync Material Pivot
        $syncMaterials = [];
        foreach ($this->selectedMaterials as $m) {
            if (!empty($m['id'])) {
                $syncMaterials[$m['id']] = ['quantity_required' => $m['qty'], 'waste_percentage' => $m['waste']];
            }
        }
        $product->materials()->sync($syncMaterials);

        // 3. Format & Sync Labor Pivot
        $syncLabors = [];
        foreach ($this->selectedLabors as $l) {
            if (!empty($l['id'])) {
                $syncLabors[$l['id']] = ['cost' => $l['cost']];
            }
        }
        $product->labors()->sync($syncLabors);

        // 4. Format & Sync Overhead Pivot
        $syncOverheads = [];
        foreach ($this->selectedOverheads as $o) {
            if (!empty($o['id'])) {
                $syncOverheads[$o['id']] = ['allocated_cost' => $o['cost']];
            }
        }
        $product->overheads()->sync($syncOverheads);

        // 5. Trigger Kalkulasi HPP!
        $product->calculateHpp();

        session()->flash('success', 'Resep produk dan HPP berhasil disimpan!');
        $this->redirectBack('backend.production.products.index');
    }
};
?>

<div class="max-w-5xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-6">{{ $product ? 'Edit Resep' : 'Buat Resep' }} Produk (BOM)</h2>

    <form wire:submit="save" class="space-y-8">
        {{-- SECTION 1: INFO PRODUK --}}
        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
            <h3 class="font-bold text-lg mb-4">Informasi Produk</h3>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm mb-1">SKU</label>
                    <input wire:model="sku" type="text" class="w-full border px-3 py-2 rounded-lg" placeholder="Contoh: KMJ-001" required>
                    @error('sku') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 text-sm mb-1">Nama Produk</label>
                    <input wire:model="name" type="text" class="w-full border px-3 py-2 rounded-lg" placeholder="Contoh: Kemeja Flanel" required>
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 text-sm mb-1">Markup Harga Jual (%)</label>
                    <input wire:model="markup_percentage" type="number" class="w-full border px-3 py-2 rounded-lg" required>
                </div>
            </div>
        </div>

        {{-- SECTION 2: BAHAN BAKU --}}
        <div>
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-bold text-lg">1. Bahan Baku (Materials)</h3>
                <button type="button" wire:click="addMaterial" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded">+ Tambah Bahan</button>
            </div>
            <div class="space-y-2">
                @foreach($selectedMaterials as $index => $material)
                <div class="flex gap-2 items-end">
                    <div class="flex-1">
                        <label class="text-xs text-gray-500">Pilih Bahan</label>
                        <select wire:model="selectedMaterials.{{ $index }}.id" class="w-full border px-3 py-2 rounded-lg">
                            <option value="">-- Pilih --</option>
                            @foreach($masterMaterials as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} (Rp{{ number_format($m->cost_per_unit, 0, ',', '.') }}/{{ $m->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-32">
                        <label class="text-xs text-gray-500">Kebutuhan (Qty)</label>
                        <input wire:model="selectedMaterials.{{ $index }}.qty" type="number" step="0.01" class="w-full border px-3 py-2 rounded-lg">
                    </div>
                    <div class="w-32">
                        <label class="text-xs text-gray-500">Waste (%)</label>
                        <input wire:model="selectedMaterials.{{ $index }}.waste" type="number" class="w-full border px-3 py-2 rounded-lg">
                    </div>
                    <button type="button" wire:click="removeMaterial({{ $index }})" class="bg-red-100 text-red-600 px-3 py-2 rounded-lg mb-0.5"><i class="fa-solid fa-trash"></i></button>
                </div>
                @endforeach
            </div>
        </div>

       {{-- SECTION 3: TENAGA KERJA --}}
        <div class="grid grid-cols-2 gap-6">
            <div>
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-bold text-lg">2. Tenaga Kerja</h3>
                    <button type="button" wire:click="addLabor" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded">+ Jasa</button>
                </div>
                <div class="space-y-2">
                    @foreach($selectedLabors as $index => $labor)
                    <div class="flex gap-2 items-end">
                        <div class="flex-1">
                            <label class="text-xs text-gray-500">Pilih Jasa</label>
                            <select wire:model="selectedLabors.{{ $index }}.id" class="w-full border px-3 py-2 rounded-lg" wire:change="$set('selectedLabors.{{ $index }}.cost', $event.target.options[$event.target.selectedIndex].dataset.cost)">
                                <option value="" data-cost="0">-- Pilih --</option>
                                @foreach($masterLabors as $l)
                                    <option value="{{ $l->id }}" data-cost="{{ $l->default_cost }}">{{ $l->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-32">
                            <label class="text-xs text-gray-500">Biaya (Rp)</label>
                            <input wire:model="selectedLabors.{{ $index }}.cost" type="number" class="w-full border px-3 py-2 rounded-lg">
                        </div>
                        <button type="button" wire:click="removeLabor({{ $index }})" class="bg-red-100 text-red-600 px-3 py-2 rounded-lg mb-0.5"><i class="fa-solid fa-trash"></i></button>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- SECTION 4: OVERHEAD --}}
            {{-- SECTION 4: OVERHEAD --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-bold text-lg">3. Overhead</h3>
                    <button type="button" wire:click="addOverhead" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded">+ Overhead</button>
                </div>
                <div class="space-y-2">
                    @foreach($selectedOverheads as $index => $overhead)
                    <div class="flex gap-2 items-end">
                        <div class="flex-1">
                            <label class="text-xs text-gray-500">Pilih Overhead</label>
                            {{-- Tambahkan wire:change dan data-cost di sini --}}
                            <select wire:model="selectedOverheads.{{ $index }}.id" class="w-full border px-3 py-2 rounded-lg" wire:change="$set('selectedOverheads.{{ $index }}.cost', $event.target.options[$event.target.selectedIndex].dataset.cost)">
                                <option value="" data-cost="0">-- Pilih --</option>
                                @foreach($masterOverheads as $o)
                                    {{-- Masukkan cost_amount ke data-cost --}}
                                    <option value="{{ $o->id }}" data-cost="{{ $o->cost_amount }}">{{ $o->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-32">
                            <label class="text-xs text-gray-500">Alokasi (Rp)</label>
                            <input wire:model="selectedOverheads.{{ $index }}.cost" type="number" class="w-full border px-3 py-2 rounded-lg">
                        </div>
                        <button type="button" wire:click="removeOverhead({{ $index }})" class="bg-red-100 text-red-600 px-3 py-2 rounded-lg mb-0.5"><i class="fa-solid fa-trash"></i></button>
                    </div>
                    @endforeach
                </div>
            </div>

        <div class="flex gap-3 pt-6 border-t">
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-bold">Simpan & Hitung HPP</button>
            <a href="{{ $backUrl ?: route('backend.production.products.index') }}" wire:navigate class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg hover:bg-gray-300 font-bold">Batal</a>
        </div>
    </form>
</div>