<?php

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use SynApps\Modules\Production\Models\Manufacture;
use SynApps\Modules\Production\Models\Product;
use SynApps\Modules\Inventory\Models\Material;
use SynApps\Modules\Accounting\Models\Account;
use SynApps\Modules\Accounting\Models\Journal;
use VmEngine\Synapse\Traits\WithReturnUrl;

new class extends Component {
    use WithReturnUrl;

    public ?Manufacture $manufacture = null;
    public $manufacture_number, $product_id, $production_date, $qty = 1, $status = 'draft';
    public $masterProducts;

    public function mount($id = null)
    {
        $this->masterProducts = Product::where('total_hpp', '>', 0)->get();
        $this->production_date = date('Y-m-d');

        if ($id) {
            $this->manufacture = Manufacture::findOrFail($id);
            $this->manufacture_number = $this->manufacture->manufacture_number;
            $this->product_id = $this->manufacture->product_id;
            $this->production_date = $this->manufacture->production_date;
            $this->qty = $this->manufacture->qty;
            $this->status = $this->manufacture->status;
        } else {
            $this->manufacture_number = 'MFG-' . date('Ymd') . '-' . rand(100, 999);
        }
    }

    public function getSelectedProductProperty()
    {
        if (!$this->product_id) return null;
        return Product::with('materials')->find($this->product_id);
    }

    public function save()
    {
        if ($this->manufacture && $this->manufacture->status === 'done') {
            session()->flash('error', 'Produksi yang sudah selesai tidak dapat diubah lagi!');
            return;
        }

        $this->validate([
            'manufacture_number' => 'required|string|unique:manufactures,manufacture_number,' . ($this->manufacture->id ?? 'NULL'),
            'product_id' => 'required|exists:products,id',
            'production_date' => 'required|date',
            'qty' => 'required|numeric|min:1',
            'status' => 'required|in:draft,on_process,done',
        ]);

        $product = Product::with(['materials', 'labors', 'overheads'])->findOrFail($this->product_id);

        if ($this->status === 'done' || $this->status === 'on_process') {
            foreach ($product->materials as $material) {
                $neededQty = ($this->qty * $material->pivot->quantity_required) * (1 + ($material->pivot->waste_percentage / 100));
                if ($material->stock < $neededQty) {
                    session()->flash('error', 'Stok bahan baku ' . $material->name . ' tidak mencukupi. Butuh: ' . $neededQty . ', Tersedia: ' . $material->stock);
                    return;
                }
            }
        }

        DB::beginTransaction();
        try {
            $manufacture = Manufacture::updateOrCreate(
                ['id' => $this->manufacture->id ?? null],
                [
                    'manufacture_number' => $this->manufacture_number,
                    'product_id' => $this->product_id,
                    'production_date' => $this->production_date,
                    'qty' => $this->qty,
                    'total_hpp' => $product->total_hpp,
                    'status' => $this->status
                ]
            );

            if ($this->status === 'done') {
                $this->processCompletion($manufacture, $product);
            }

            DB::commit();
            session()->flash('success', 'Data eksekusi produksi berhasil disimpan!');
            $this->redirectBack('backend.production.manufactures.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses produksi: ' . $e->getMessage());
        }
    }

    private function processCompletion(Manufacture $manufacture, Product $product)
    {
        $totalMaterialCost = 0;
        foreach ($product->materials as $material) {
            $neededQty = ($manufacture->qty * $material->pivot->quantity_required) * (1 + ($material->pivot->waste_percentage / 100));
            $totalMaterialCost += ($neededQty * $material->cost_per_unit);

            $newMaterialStock = $material->stock - $neededQty;
            $material->update(['stock' => $newMaterialStock]);

            $material->ledgers()->create([
                'transaction_type' => 'out',
                'reference_type' => 'Production',
                'reference_id' => $manufacture->id,
                'qty' => $neededQty,
                'balance_after' => $newMaterialStock,
                'description' => 'Dipakai untuk Produksi ' . $product->name . ' (No: ' . $manufacture->manufacture_number . ')'
            ]);
        }

        $newProductStock = $product->stock + $manufacture->qty;
        $product->update(['stock' => $newProductStock]);
        
        $product->ledgers()->create([
            'transaction_type' => 'in',
            'reference_type' => 'Production',
            'reference_id' => $manufacture->id,
            'qty' => $manufacture->qty,
            'balance_after' => $newProductStock,
            'description' => 'Hasil Produksi (No: ' . $manufacture->manufacture_number . ')'
        ]);

        $journal = Journal::create([
            'journal_number' => 'J.MFG.' . str_pad($manufacture->id, 4, '0', STR_PAD_LEFT),
            'transaction_date' => $manufacture->production_date,
            'reference_type' => 'Production',
            'reference_id' => $manufacture->id,
            'description' => 'Produksi ' . $manufacture->qty . ' pcs ' . $product->name . ' (No: ' . $manufacture->manufacture_number . ')'
        ]);

        $akunProduk = Account::where('account_code', '1-1003')->first()->id;
        $akunBahan = Account::where('account_code', '1-1002')->first()->id;
        $akunKas = Account::where('account_code', '1-1001')->first()->id;

        $totalHppKeseluruhan = $manufacture->total_hpp * $manufacture->qty;
        $totalJasaOverhead = $totalHppKeseluruhan - $totalMaterialCost;

        $journal->details()->create([
            'account_id' => $akunProduk,
            'debit' => $totalHppKeseluruhan,
            'credit' => 0
        ]);

        $journal->details()->create([
            'account_id' => $akunBahan,
            'debit' => 0,
            'credit' => $totalMaterialCost
        ]);

        if ($totalJasaOverhead > 0) {
            $journal->details()->create([
                'account_id' => $akunKas,
                'debit' => 0,
                'credit' => $totalJasaOverhead
            ]);
        }
    }
};
?>

<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-6">{{ $manufacture ? 'Detail' : 'Mulai' }} Eksekusi Produksi</h2>

    @if (session()->has('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    @if($status === 'done' && $manufacture)
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
        <p class="text-sm text-green-700 font-bold">
            <i class="fa-solid fa-check-circle mr-2"></i> Produksi telah selesai. Stok bahan baku otomatis terpotong, stok barang jadi bertambah, dan jurnal telah dibukukan.
        </p>
    </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">No. Produksi</label>
                <input wire:model="manufacture_number" type="text" class="w-full border px-3 py-2 rounded-lg bg-gray-100" readonly>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Produksi</label>
                <input wire:model="production_date" type="date" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'done' && $manufacture) disabled @endif>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Barang Jadi yang akan Diproduksi</label>
            <select wire:model.live="product_id" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'done' && $manufacture) disabled @endif>
                <option value="">-- Pilih Produk (BOM) --</option>
                @foreach($masterProducts as $p)
                    <option value="{{ $p->id }}">{{ $p->sku }} - {{ $p->name }} (HPP/pcs: Rp{{ number_format($p->total_hpp, 0, ',', '.') }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Target Produksi (QTY pcs)</label>
                <input wire:model.live="qty" type="number" min="1" class="w-full border px-3 py-2 rounded-lg text-xl font-bold text-blue-700" required @if($status === 'done' && $manufacture) disabled @endif>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Status Produksi</label>
                <select wire:model="status" class="w-full border px-3 py-2 rounded-lg font-bold" @if($status === 'done' && $manufacture) disabled @endif>
                    <option value="draft">DRAFT (Perencanaan)</option>
                    <option value="on_process">PROSES (Sedang Dijahit/Dikerjakan)</option>
                    <option value="done">SELESAI (Simpan ke Gudang & Potong Stok)</option>
                </select>
            </div>
        </div>

        @if($this->selected_product)
        <div class="mt-6 border rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="text-sm font-bold text-gray-700">Estimasi Kebutuhan Bahan Baku</h3>
            </div>
            <div class="p-4">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-gray-500 border-b">
                            <th class="pb-2">Bahan Baku</th>
                            <th class="pb-2">Kebutuhan /pcs</th>
                            <th class="pb-2">Total Kebutuhan</th>
                            <th class="pb-2">Stok Tersedia</th>
                            <th class="pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->selected_product->materials as $material)
                            @php
                                $kebutuhanPerPcs = $material->pivot->quantity_required * (1 + ($material->pivot->waste_percentage / 100));
                                $totalKebutuhan = $kebutuhanPerPcs * (int)$qty;
                                $isCukup = $material->stock >= $totalKebutuhan;
                            @endphp
                            <tr class="border-b last:border-0">
                                <td class="py-2">{{ $material->name }}</td>
                                <td class="py-2">{{ number_format($kebutuhanPerPcs, 2) }} {{ $material->unit }}</td>
                                <td class="py-2 font-bold">{{ number_format($totalKebutuhan, 2) }} {{ $material->unit }}</td>
                                <td class="py-2">{{ number_format($material->stock, 2) }} {{ $material->unit }}</td>
                                <td class="py-2">
                                    @if($isCukup)
                                        <span class="text-green-600 bg-green-100 px-2 py-1 rounded text-xs">Cukup</span>
                                    @else
                                        <span class="text-red-600 bg-red-100 px-2 py-1 rounded text-xs">Kurang</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="flex gap-3 pt-6 border-t">
            @if(!($status === 'done' && $manufacture))
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-bold">Simpan Proses</button>
            @endif
            <a href="{{ $backUrl ?: route('backend.production.manufactures.index') }}" wire:navigate class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg hover:bg-gray-300 font-bold">Kembali</a>
        </div>
    </form>
</div>