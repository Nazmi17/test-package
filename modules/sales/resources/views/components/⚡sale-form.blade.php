<?php

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use SynApps\Modules\Sales\Models\Sale;
use SynApps\Modules\Sales\Models\SaleDetail;
use SynApps\Modules\Production\Models\Product;
use SynApps\Modules\Accounting\Models\Account;
use SynApps\Modules\Accounting\Models\Journal;
use VmEngine\Synapse\Traits\WithReturnUrl;

new class extends Component {
    use WithReturnUrl;

    public ?Sale $sale = null;
    public $invoice_number, $customer_name, $sale_date, $payment_status = 'paid', $status = 'draft';
    public $details = [];
    public $masterProducts;

    public function mount($id = null)
    {
        // Hanya tampilkan produk yang ada stoknya untuk dijual (bisa juga ambil semua)
        $this->masterProducts = Product::where('stock', '>', 0)->get();
        $this->sale_date = date('Y-m-d');

        if ($id) {
            $this->sale = Sale::with('details')->findOrFail($id);
            $this->invoice_number = $this->sale->invoice_number;
            $this->customer_name = $this->sale->customer_name;
            $this->sale_date = $this->sale->sale_date;
            $this->payment_status = $this->sale->payment_status;
            $this->status = $this->sale->status;

            foreach ($this->sale->details as $d) {
                $this->details[] = ['product_id' => $d->product_id, 'qty' => $d->qty, 'price' => $d->price];
            }
        } else {
            $this->invoice_number = 'INV-OUT-' . date('Ymd') . '-' . rand(100, 999);
            $this->addDetail();
        }
    }

    public function addDetail() { $this->details[] = ['product_id' => '', 'qty' => 1, 'price' => 0]; }
    public function removeDetail($index) { unset($this->details[$index]); $this->details = array_values($this->details); }

    public function save()
    {
        if ($this->sale && $this->sale->status === 'completed') {
            session()->flash('error', 'Transaksi yang sudah diselesaikan tidak dapat diubah!');
            return;
        }

        $this->validate([
            'invoice_number' => 'required|string|unique:sales,invoice_number,' . ($this->sale->id ?? 'NULL'),
            'customer_name' => 'required|string',
            'sale_date' => 'required|date',
            'details.*.product_id' => 'required',
            'details.*.qty' => 'required|numeric|min:1',
            'details.*.price' => 'required|numeric|min:0',
        ]);

        $totalAmount = 0;
        foreach ($this->details as $d) {
            $totalAmount += ($d['qty'] * $d['price']);
        }

        DB::beginTransaction();
        try {
            $sale = Sale::updateOrCreate(
                ['id' => $this->sale->id ?? null],
                [
                    'invoice_number' => $this->invoice_number,
                    'customer_name' => $this->customer_name,
                    'sale_date' => $this->sale_date,
                    'total_amount' => $totalAmount,
                    'payment_status' => $this->payment_status,
                    'status' => $this->status
                ]
            );

            $sale->details()->delete();
            $detailModels = [];
            foreach ($this->details as $d) {
                $detailModels[] = new SaleDetail([
                    'product_id' => $d['product_id'],
                    'qty' => $d['qty'],
                    'price' => $d['price'],
                    'subtotal' => $d['qty'] * $d['price'],
                ]);
            }
            $sale->details()->saveMany($detailModels);

            if ($this->status === 'completed') {
                $this->processCompletion($sale);
            }

            DB::commit();
            session()->flash('success', 'Transaksi penjualan berhasil disimpan!');
            $this->redirectBack('backend.sales.sales.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    private function processCompletion(Sale $sale)
    {
        $totalHpp = 0;

        // 1. Potong Stok Barang Jadi
        foreach ($sale->details as $detail) {
            $product = Product::findOrFail($detail->product_id);
            
            // Cek jika stok tidak cukup (Opsional, tapi bagus buat keamanan)
            if ($product->stock < $detail->qty) {
                throw new \Exception("Stok {$product->name} tidak cukup! Sisa stok: {$product->stock}");
            }

            $newStock = $product->stock - $detail->qty;
            $product->update(['stock' => $newStock]);

            // Hitung akumulasi HPP untuk Jurnal
            $totalHpp += ($product->total_hpp * $detail->qty);

            // Catat Ledger (Barang Keluar)
            $product->ledgers()->create([
                'transaction_type' => 'out',
                'reference_type' => 'Sale',
                'reference_id' => $sale->id,
                'qty' => $detail->qty,
                'balance_after' => $newStock,
                'description' => 'Terjual ke ' . $sale->customer_name . ' (Faktur: ' . $sale->invoice_number . ')'
            ]);
        }

        // 2. Buat Jurnal Penjualan
        $journal = Journal::create([
            'journal_number' => 'J.SLS.' . str_pad($sale->id, 4, '0', STR_PAD_LEFT),
            'transaction_date' => $sale->sale_date,
            'reference_type' => 'Sale',
            'reference_id' => $sale->id,
            'description' => 'Penjualan ke ' . $sale->customer_name . ' (Inv: ' . $sale->invoice_number . ')'
        ]);

        $akunKas = Account::where('account_code', '1-1001')->first()->id;
        $akunPiutang = Account::where('account_code', '1-1004')->first()->id;
        $akunPendapatan = Account::where('account_code', '4-4001')->first()->id;
        $akunHPP = Account::where('account_code', '5-5001')->first()->id;
        $akunPersediaanBarang = Account::where('account_code', '1-1003')->first()->id;

        // Jurnal 1: Kas/Piutang (Debit) pada Pendapatan (Kredit)
        $debitAkunId = ($sale->payment_status == 'paid') ? $akunKas : $akunPiutang;
        $journal->details()->create([
            'account_id' => $debitAkunId,
            'debit' => $sale->total_amount,
            'credit' => 0
        ]);
        $journal->details()->create([
            'account_id' => $akunPendapatan,
            'debit' => 0,
            'credit' => $sale->total_amount
        ]);

        // Jurnal 2: HPP (Debit) pada Persediaan Barang (Kredit)
        if ($totalHpp > 0) {
            $journal->details()->create([
                'account_id' => $akunHPP,
                'debit' => $totalHpp,
                'credit' => 0
            ]);
            $journal->details()->create([
                'account_id' => $akunPersediaanBarang,
                'debit' => 0,
                'credit' => $totalHpp
            ]);
        }
    }
};
?>

<div class="max-w-5xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-6">{{ $sale ? 'Detail' : 'Catat' }} Penjualan</h2>

    @if($status === 'completed' && $sale)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <p class="text-sm text-yellow-700">
            <i class="fa-solid fa-lock mr-2"></i> Transaksi selesai. Stok barang jadi telah dipotong dan jurnal akuntansi telah terbentuk.
        </p>
    </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-3 gap-4 p-4 border rounded-lg bg-gray-50">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">No. Faktur</label>
                <input wire:model="invoice_number" type="text" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'completed' && $sale) disabled @endif>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Pelanggan</label>
                <input wire:model="customer_name" type="text" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'completed' && $sale) disabled @endif>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Transaksi</label>
                <input wire:model="sale_date" type="date" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'completed' && $sale) disabled @endif>
            </div>
        </div>

        <div>
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-bold">Daftar Barang yang Dijual</h3>
                @if(!($status === 'completed' && $sale))
                    <button type="button" wire:click="addDetail" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded">+ Tambah Barang</button>
                @endif
            </div>
            
            <div class="space-y-2">
                @foreach($details as $index => $detail)
                <div class="flex gap-2 items-end">
                    <div class="flex-1">
                        <label class="text-xs text-gray-500">Pilih Barang Jadi</label>
                        {{-- Auto-fill harga jual saat produk dipilih --}}
                        <select wire:model="details.{{ $index }}.product_id" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'completed' && $sale) disabled @endif wire:change="$set('details.{{ $index }}.price', $event.target.options[$event.target.selectedIndex].dataset.price)">
                            <option value="" data-price="0">-- Pilih Produk --</option>
                            @foreach($masterProducts as $p)
                                <option value="{{ $p->id }}" data-price="{{ $p->suggested_price }}">{{ $p->sku }} - {{ $p->name }} (Stok: {{ $p->stock }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-24">
                        <label class="text-xs text-gray-500">QTY (Pcs)</label>
                        <input wire:model="details.{{ $index }}.qty" type="number" min="1" class="w-full border px-3 py-2 rounded-lg" required @if($status === 'completed' && $sale) disabled @endif>
                    </div>
                    <div class="w-48">
                        <label class="text-xs text-gray-500">Harga Jual Satuan (Rp)</label>
                        <input wire:model="details.{{ $index }}.price" type="number" class="w-full border px-3 py-2 rounded-lg text-green-700 font-bold" required @if($status === 'completed' && $sale) disabled @endif>
                    </div>
                    @if(!($status === 'completed' && $sale))
                        <button type="button" wire:click="removeDetail({{ $index }})" class="bg-red-100 text-red-600 px-3 py-2 rounded-lg mb-0.5"><i class="fa-solid fa-trash"></i></button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 p-4 border rounded-lg bg-green-50 border-green-200">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Status Pembayaran</label>
                <select wire:model="payment_status" class="w-full border px-3 py-2 rounded-lg" @if($status === 'completed' && $sale) disabled @endif>
                    <option value="paid">LUNAS (Cash/Transfer)</option>
                    <option value="unpaid">PIUTANG (Belum Bayar)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Status Transaksi</label>
                <select wire:model="status" class="w-full border px-3 py-2 rounded-lg font-bold text-green-800" @if($status === 'completed' && $sale) disabled @endif>
                    <option value="draft">DRAFT (Belum Selesai)</option>
                    <option value="completed">SELESAI (Potong Stok & Jurnal)</option>
                </select>
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t">
            @if(!($status === 'completed' && $sale))
                <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 font-bold">Simpan Penjualan</button>
            @endif
            <a href="{{ $backUrl ?: route('backend.sales.sales.index') }}" wire:navigate class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg hover:bg-gray-300 font-bold">Kembali</a>
        </div>
    </form>
</div>