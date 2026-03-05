<?php

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use SynApps\Modules\Purchasing\Models\Purchase;
use SynApps\Modules\Purchasing\Models\PurchaseDetail;
use SynApps\Modules\Inventory\Models\Material;
use SynApps\Modules\Accounting\Models\Account;
use SynApps\Modules\Accounting\Models\Journal;
use VmEngine\Synapse\Traits\WithReturnUrl;

new class extends Component {
    use WithReturnUrl;

    public ?Purchase $purchase = null;
    public $invoice_number, $supplier_name, $purchase_date, $payment_status = 'paid', $status = 'draft';
    public $details = []; // Format: [['material_id' => '', 'qty' => 0, 'price' => 0]]
    public $masterMaterials;

    public function mount($id = null)
    {
        $this->masterMaterials = Material::all();
        $this->purchase_date = date('Y-m-d');

        if ($id) {
            $this->purchase = Purchase::with('details')->findOrFail($id);
            $this->invoice_number = $this->purchase->invoice_number;
            $this->supplier_name = $this->purchase->supplier_name;
            $this->purchase_date = $this->purchase->purchase_date;
            $this->payment_status = $this->purchase->payment_status;
            $this->status = $this->purchase->status;

            foreach ($this->purchase->details as $d) {
                $this->details[] = ['material_id' => $d->material_id, 'qty' => $d->qty, 'price' => $d->price];
            }
        } else {
            // Generate nomor invoice unik (INV-TahunBulanHari-Random)
            $this->invoice_number = 'INV-' . date('Ymd') . '-' . rand(100, 999);
            $this->addDetail();
        }
    }

    public function addDetail() { $this->details[] = ['material_id' => '', 'qty' => 0, 'price' => 0]; }
    public function removeDetail($index) { unset($this->details[$index]); $this->details = array_values($this->details); }

    public function save()
    {
        // Cegah edit jika status sudah completed (Selesai)
        if ($this->purchase && $this->purchase->status === 'completed') {
            session()->flash('error', 'Transaksi yang sudah selesai dan dijurnal tidak dapat diubah lagi!');
            return;
        }

        $this->validate([
            'invoice_number' => 'required|string|unique:purchases,invoice_number,' . ($this->purchase->id ?? 'NULL'),
            'supplier_name' => 'required|string',
            'purchase_date' => 'required|date',
            'details.*.material_id' => 'required',
            'details.*.qty' => 'required|numeric|min:0.1',
            'details.*.price' => 'required|numeric|min:0',
        ]);

        // Hitung Grand Total
        $totalAmount = 0;
        foreach ($this->details as $d) {
            $totalAmount += ($d['qty'] * $d['price']);
        }

        DB::beginTransaction();
        try {
            // 1. Simpan Header Transaksi
            $purchase = Purchase::updateOrCreate(
                ['id' => $this->purchase->id ?? null],
                [
                    'invoice_number' => $this->invoice_number,
                    'supplier_name' => $this->supplier_name,
                    'purchase_date' => $this->purchase_date,
                    'total_amount' => $totalAmount,
                    'payment_status' => $this->payment_status,
                    'status' => $this->status
                ]
            );

            // 2. Simpan Detail Transaksi (Hapus yang lama, insert baru)
            $purchase->details()->delete();
            $detailModels = [];
            foreach ($this->details as $d) {
                $detailModels[] = new PurchaseDetail([
                    'material_id' => $d['material_id'],
                    'qty' => $d['qty'],
                    'price' => $d['price'],
                    'subtotal' => $d['qty'] * $d['price'],
                ]);
            }
            $purchase->details()->saveMany($detailModels);

            // 3. JIKA STATUS SELESAI, JALANKAN PROSES AKUNTANSI & INVENTORY
            if ($this->status === 'completed') {
                $this->processCompletion($purchase);
            }

            DB::commit();
            session()->flash('success', 'Transaksi pembelian berhasil disimpan!');
            $this->redirectBack('backend.purchasing.purchases.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // --- FUNGSI AUTO MAGIC (KARTU STOK & JURNAL) ---
    private function processCompletion(Purchase $purchase)
    {
        // A. Update Stok & Kartu Stok (Ledger)
        foreach ($purchase->details as $detail) {
            $material = Material::findOrFail($detail->material_id);
            $newStock = $material->stock + $detail->qty;

            // Update Master Stok
            $material->update(['stock' => $newStock]);

            // Catat ke Stock Ledger
            $material->ledgers()->create([
                'transaction_type' => 'in',
                'reference_type' => 'Purchase',
                'reference_id' => $purchase->id,
                'qty' => $detail->qty,
                'balance_after' => $newStock,
                'description' => 'Pembelian dari ' . $purchase->supplier_name . ' (Faktur: ' . $purchase->invoice_number . ')'
            ]);
        }

        // B. Buat Jurnal Akuntansi
        $journal = Journal::create([
            'journal_number' => 'J.PRC.' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT),
            'transaction_date' => $purchase->purchase_date,
            'reference_type' => 'Purchase',
            'reference_id' => $purchase->id,
            'description' => 'Pembelian Bahan Baku dari ' . $purchase->supplier_name . ' (Inv: ' . $purchase->invoice_number . ')'
        ]);

        // Ambil ID Akun dari Master Data Akuntansi
        $akunPersediaan = Account::where('account_code', '1-1002')->first()->id; // Asset: Nambah di Debit
        $akunKas = Account::where('account_code', '1-1001')->first()->id;        // Asset: Berkurang di Kredit
        $akunHutang = Account::where('account_code', '2-2001')->first()->id;     // Liability: Nambah di Kredit

        // Jurnal Debit: Persediaan Bahan Baku Bertambah
        $journal->details()->create([
            'account_id' => $akunPersediaan,
            'debit' => $purchase->total_amount,
            'credit' => 0
        ]);

        // Jurnal Kredit: Uang Kas Berkurang ATAU Hutang Bertambah
        $kreditAkunId = ($purchase->payment_status == 'paid') ? $akunKas : $akunHutang;
        $journal->details()->create([
            'account_id' => $kreditAkunId,
            'debit' => 0,
            'credit' => $purchase->total_amount
        ]);
    }
};
?>

<div class="max-w-5xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-6">{{ $purchase ? 'Detail' : 'Catat' }} Pembelian Bahan</h2>

    {{-- Notifikasi peringatan jika sudah selesai --}}
    @if($status === 'completed' && $purchase)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <p class="text-sm text-yellow-700">
            <i class="fa-solid fa-lock mr-2"></i> Transaksi ini sudah diselesaikan. Stok dan Jurnal Akuntansi telah terbentuk. Anda tidak dapat merubah datanya lagi.
        </p>
    </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-3 gap-4 p-4 border rounded-lg bg-gray-50">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">No. Faktur / Invoice</label>
                <input wire:model="invoice_number" type="text" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'completed' && $purchase) disabled @endif>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Supplier / Toko</label>
                <input wire:model="supplier_name" type="text" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'completed' && $purchase) disabled @endif>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Beli</label>
                <input wire:model="purchase_date" type="date" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'completed' && $purchase) disabled @endif>
            </div>
        </div>

        <div>
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-bold">Rincian Barang Dibeli</h3>
                @if(!($status === 'completed' && $purchase))
                    <button type="button" wire:click="addDetail" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded">+ Tambah Baris</button>
                @endif
            </div>
            
            <div class="space-y-2">
                @foreach($details as $index => $detail)
                <div class="flex gap-2 items-end">
                    <div class="flex-1">
                        <label class="text-xs text-gray-500">Bahan Baku</label>
                        <select wire:model="details.{{ $index }}.material_id" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'completed' && $purchase) disabled @endif>
                            <option value="">-- Pilih Bahan --</option>
                            @foreach($masterMaterials as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} (Rp{{ number_format($m->cost_per_unit, 0, ',', '.') }}/{{ $m->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-24">
                        <label class="text-xs text-gray-500">QTY Beli</label>
                        <input wire:model="details.{{ $index }}.qty" type="number" step="0.01" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'completed' && $purchase) disabled @endif>
                    </div>
                    <div class="w-40">
                        <label class="text-xs text-gray-500">Harga Beli Aktual (Rp)</label>
                        <input wire:model="details.{{ $index }}.price" type="number" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'completed' && $purchase) disabled @endif>
                    </div>
                    @if(!($status === 'completed' && $purchase))
                        <button type="button" wire:click="removeDetail({{ $index }})" class="bg-red-100 text-red-600 px-3 py-2 rounded-lg mb-0.5"><i class="fa-solid fa-trash"></i></button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 p-4 border rounded-lg bg-blue-50 border-blue-200">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Status Pembayaran</label>
                <select wire:model="payment_status" class="w-full border px-3 py-2 rounded-lg" @if($status === 'completed' && $purchase) disabled @endif>
                    <option value="paid">LUNAS (Bayar Kontan)</option>
                    <option value="unpaid">NGUTANG (Pembayaran Tempo)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Status Transaksi</label>
                <select wire:model="status" class="w-full border px-3 py-2 rounded-lg font-bold text-blue-800" @if($status === 'completed' && $purchase) disabled @endif>
                    <option value="draft">DRAFT (Masih Bisa Diedit)</option>
                    <option value="completed">SELESAI (Simpan & Masuk Pembukuan)</option>
                </select>
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t">
            @if(!($status === 'completed' && $purchase))
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 font-bold">Simpan Transaksi</button>
            @endif
            <a href="{{ $backUrl ?: route('backend.purchasing.purchases.index') }}" wire:navigate class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg hover:bg-gray-300 font-bold">Kembali</a>
        </div>
    </form>
</div>