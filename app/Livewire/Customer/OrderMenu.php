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

    public $activeCustomers = [];
    public $selectedCustomerId;
    public $newPersonName = '';

    public function mount()
    {
        if (!session()->has('customer_id')) {
            return redirect()->route('home');
        }

        $this->customer = SessionCustomer::with('tableSession.table', 'tableSession.customers')->find(session('customer_id'));

        if (!$this->customer || $this->customer->tableSession->status !== 'active') {
            session()->forget('customer_id');


            session()->forget('cart');

            return redirect()->route('home');
        }

        $this->tableSession = $this->customer->tableSession;
        $this->table = $this->tableSession->table;

        $this->loadCustomers();
        $this->selectedCustomerId = $this->customer->id;
    }

    #[On('refreshCustomers')]
    public function loadCustomers()
    {
        $this->activeCustomers = $this->tableSession->customers()->get();
    }

    public function addNewPerson()
    {
        $this->validate([
            'newPersonName' => 'required|string|min:2|max:20'
        ]);

        $newCust = SessionCustomer::create([
            'table_session_id' => $this->tableSession->id,
            'display_name' => $this->newPersonName,
            'device_identifier' => 'shared-from-' . $this->customer->id,
            'is_host' => false
        ]);

        $this->loadCustomers();
        $this->selectedCustomerId = $newCust->id;
        $this->newPersonName = '';

        $this->dispatch('person-added');
    }

    public function render()
    {
        $categories = Category::where('is_active', true)
            ->with([
                'products' => function ($query) {
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
