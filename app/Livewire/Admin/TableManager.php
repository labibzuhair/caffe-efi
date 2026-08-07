<?php

namespace App\Livewire\Admin;

use App\Models\Table;
use App\Models\Setting; // Pastikan model Setting dipanggil
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
// KITA HAPUS #[Title] STATIS DI SINI
class TableManager extends Component
{
    use WithPagination;

    public $table_number = '';
    public $status = 'available';

    public $tableId = null;
    public $isEditMode = false;
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset(['table_number', 'status', 'tableId', 'isEditMode']);
        $this->resetValidation();
    }

    public function save()
    {
        $rules = [
            'table_number' => 'required|string|max:50|unique:tables,table_number,' . $this->tableId,
            'status' => 'required|string|in:available,maintenance',
        ];

        $this->validate($rules);

        $data = [
            'table_number' => $this->table_number,
            'status' => $this->status
        ];

        if (!$this->tableId) {
            $data['qr_token'] = Str::random(12);
        }

        Table::updateOrCreate(
            ['id' => $this->tableId],
            $data
        );

        session()->flash('message', $this->isEditMode ? 'Data meja berhasil diperbarui!' : 'Meja baru berhasil ditambahkan!');
        $this->resetForm();
    }

    public function edit($id)
    {
        $table = Table::findOrFail($id);
        $this->tableId = $table->id;
        $this->table_number = $table->table_number;

        if ($table->status === 'occupied') {
            session()->flash('error', 'Meja ini sedang digunakan pelanggan, tidak bisa diedit saat ini.');
            $this->resetForm();
            return;
        }

        $this->status = $table->status;
        $this->isEditMode = true;
    }

    public function toggleStatus($id)
    {
        $table = Table::findOrFail($id);

        if ($table->status === 'occupied') {
            session()->flash('error', 'Meja sedang digunakan, selesaikan pesanan terlebih dahulu!');
            return;
        }

        $table->update(['status' => $table->status === 'available' ? 'maintenance' : 'available']);
    }

    public function delete($id)
    {
        $table = Table::findOrFail($id);

        if ($table->status === 'occupied') {
            session()->flash('error', 'Gagal! Meja sedang digunakan pelanggan.');
            return;
        }

        $table->delete();
        session()->flash('message', 'Meja berhasil dihapus!');
    }

    public function getQrCodeUrl($qrToken)
    {
        return url('/meja/' . $qrToken);
    }
    public function render()
    {
        $tables = Table::where('table_number', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(12);

        // AMBIL PENGATURAN TOKO UNTUK DIKIRIM KE VIEW & TITLE
        $setting = Setting::first();
        $storeName = $setting->store_name ?? 'CaffePOS';

        return view('livewire.admin.table-manager', [
            'tables' => $tables,
            'storeName' => $storeName // Kirim variabel ke Blade
        ])->title('Manajemen Meja - ' . $storeName); // Atur Title Browser secara dinamis
    }
}
