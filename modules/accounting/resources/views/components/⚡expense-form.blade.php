<?php

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use SynApps\Modules\Accounting\Models\Expense;
use SynApps\Modules\Accounting\Models\Account;
use SynApps\Modules\Accounting\Models\Journal;
use VmEngine\Synapse\Traits\WithReturnUrl;

new class extends Component {
    use WithReturnUrl;

    public $expense_number, $expense_date, $expense_account_id, $payment_account_id, $amount, $description;
    public $expenseAccounts = [];
    public $paymentAccounts = [];

    public function mount()
    {
        $this->expense_date = date('Y-m-d');
        $this->expense_number = 'EXP-' . date('Ymd') . '-' . rand(100, 999);

        // Auto-Generate akun beban umum jika belum ada (Biar kamu nggak repot nambahin manual)
        Account::firstOrCreate(['account_code' => '5-5004'], ['account_name' => 'Beban Gaji & Upah (Non-Produksi)', 'account_type' => 'Expense', 'is_system_default' => 1]);
        Account::firstOrCreate(['account_code' => '5-5005'], ['account_name' => 'Beban Listrik, Air & Internet', 'account_type' => 'Expense', 'is_system_default' => 1]);
        Account::firstOrCreate(['account_code' => '5-5006'], ['account_name' => 'Beban Pemasaran & Iklan', 'account_type' => 'Expense', 'is_system_default' => 1]);
        Account::firstOrCreate(['account_code' => '5-5099'], ['account_name' => 'Beban Operasional Lain-lain', 'account_type' => 'Expense', 'is_system_default' => 1]);

        // Ambil data untuk Dropdown
        // Akun Pembayaran (Asset) -> Biasanya Kas/Bank
        $this->paymentAccounts = Account::where('account_type', 'Asset')->get();
        
        // Akun Pengeluaran (Expense) -> Kecuali HPP, Tenaga Kerja Langsung, dan Overhead Produksi (Karena itu urusan pabrik)
        $this->expenseAccounts = Account::where('account_type', 'Expense')
            ->whereNotIn('account_code', ['5-5001', '5-5002', '5-5003'])
            ->get();
    }

    public function save()
    {
        $this->validate([
            'expense_number' => 'required|unique:expenses,expense_number',
            'expense_date' => 'required|date',
            'expense_account_id' => 'required|exists:accounts,id',
            'payment_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1000',
            'description' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan Bukti Pengeluaran
            $expense = Expense::create([
                'expense_number' => $this->expense_number,
                'expense_date' => $this->expense_date,
                'expense_account_id' => $this->expense_account_id,
                'payment_account_id' => $this->payment_account_id,
                'amount' => $this->amount,
                'description' => $this->description,
            ]);

            // 2. Buat Jurnal Akuntansi Otomatis
            $journal = Journal::create([
                'journal_number' => 'J.EXP.' . str_pad($expense->id, 4, '0', STR_PAD_LEFT),
                'transaction_date' => $this->expense_date,
                'reference_type' => 'Expense',
                'reference_id' => $expense->id,
                'description' => 'Pengeluaran Kas: ' . $this->description
            ]);

            // Jurnal Debit: Beban Bertambah
            $journal->details()->create([
                'account_id' => $this->expense_account_id,
                'debit' => $this->amount,
                'credit' => 0
            ]);

            // Jurnal Kredit: Aset (Kas/Bank) Berkurang
            $journal->details()->create([
                'account_id' => $this->payment_account_id,
                'debit' => 0,
                'credit' => $this->amount
            ]);

            DB::commit();
            session()->flash('success', 'Pengeluaran berhasil dicatat dan dijurnal!');
            $this->redirectBack('backend.accounting.expenses.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal mencatat pengeluaran: ' . $e->getMessage());
        }
    }
};
?>

<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-sm">
    <h2 class="text-2xl font-bold mb-6 text-orange-800">Catat Pengeluaran Kas (Expense)</h2>

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">No. Bukti Transaksi</label>
                <input wire:model="expense_number" type="text" class="w-full border px-3 py-2 rounded-lg bg-gray-100" readonly>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Pengeluaran</label>
                <input wire:model="expense_date" type="date" class="w-full border px-3 py-2 rounded-lg" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-red-50 p-4 border border-red-200 rounded-lg">
                <label class="block text-xs font-bold text-red-800 mb-1">Kategori Pengeluaran (Beban)</label>
                <select wire:model="expense_account_id" class="w-full border px-3 py-2 rounded-lg" required>
                    <option value="">-- Pilih Jenis Beban --</option>
                    @foreach($expenseAccounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-red-600 mt-1">*Beban yang dipilih akan masuk ke Laba Rugi.</p>
            </div>
            <div class="bg-blue-50 p-4 border border-blue-200 rounded-lg">
                <label class="block text-xs font-bold text-blue-800 mb-1">Sumber Dana (Dibayar Dari)</label>
                <select wire:model="payment_account_id" class="w-full border px-3 py-2 rounded-lg" required>
                    <option value="">-- Pilih Akun Kas/Bank --</option>
                    @foreach($paymentAccounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Nominal (Rp)</label>
            <input wire:model="amount" type="number" min="1000" class="w-full border px-3 py-2 rounded-lg text-2xl font-black text-orange-600" required placeholder="Contoh: 500000">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Keterangan / Rincian Singkat</label>
            <textarea wire:model="description" class="w-full border px-3 py-2 rounded-lg" rows="2" required placeholder="Contoh: Pembayaran listrik toko bulan Maret"></textarea>
        </div>

        <div class="flex gap-3 pt-6 border-t">
            <button type="submit" class="bg-orange-600 text-white px-8 py-3 rounded-lg hover:bg-orange-700 font-bold shadow-md">Simpan & Jurnal</button>
            <a href="{{ $backUrl ?: route('backend.accounting.expenses.index') }}" wire:navigate class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg hover:bg-gray-300 font-bold">Batal</a>
        </div>
    </form>
</div>