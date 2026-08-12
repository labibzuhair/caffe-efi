<?php

namespace App\Livewire\Customer;

use App\Models\Category;
use App\Models\SessionCustomer;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.customer')]
#[Title('Menu Pemesanan')]
class OrderMenu extends Component
{
    public $customer;
    public $tableSession;
    public $table;

    // Properti Khusus Split-Bill
    public $activeCustomers = [];
    public $selectedCustomerId;
    public $newPersonName = '';

    public function mount()
    {
        // PROTEKSI: Jika tidak ada sesi customer, lempar kembali ke halaman depan
        if (!session()->has('customer_id')) {
            return redirect()->route('home');
        }

        $this->customer = SessionCustomer::with('tableSession.table', 'tableSession.customers')->find(session('customer_id'));

        // Jika data hilang atau mejanya sudah selesai/ditutup kasir, usir ke halaman depan
        if (!$this->customer || $this->customer->tableSession->status !== 'active') {
            session()->forget('customer_id');

            // ==========================================
            // PERBAIKAN: Bersihkan juga keranjangnya saat sesi hangus
            // ==========================================
            session()->forget('cart');

            return redirect()->route('home');
        }

        $this->tableSession = $this->customer->tableSession;
        $this->table = $this->tableSession->table;

        $this->loadCustomers();
        $this->selectedCustomerId = $this->customer->id;
    }

    #[On('echo:customer-table-{tableSession.id},.customer.joined')]
    public function loadCustomers()
    {
        $this->activeCustomers = $this->tableSession->customers()->get();
    }

    // Fungsi untuk menambah teman dari HP yang sama
    public function addNewPerson()
    {
        $this->validate([
            'newPersonName' => 'required|string|min:2|max:20'
        ]);

        $newCust = SessionCustomer::create([
            'table_session_id' => $this->tableSession->id,
            'display_name' => $this->newPersonName,
            'device_identifier' => 'shared-from-' . $this->customer->id, // Penanda kalau ini ditambah dari HP teman
            'is_host' => false
        ]);

        $this->loadCustomers();
        $this->selectedCustomerId = $newCust->id; // Otomatis pindahkan pilihan ke teman baru
        $this->newPersonName = ''; // Kosongkan input

        // Kirim event untuk menutup modal di frontend
        $this->dispatch('person-added');
    }

    public function render()
    {
        $categories = Category::where('is_active', true)
            ->with([
                'products' => function ($query) {
                    // PERUBAHAN: Hapus ->where('is_active', true) agar menu HABIS tetap dikirim ke layar
                    $query->with('addons');
                }
            ])->get();

        $setting = Setting::first();

        return view('livewire.customer.order-menu', [
            'categories' => $categories,
            'setting' => $setting,
        ]);
    }
}
