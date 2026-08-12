<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use App\Models\SessionCustomer;
use App\Models\Setting;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On; // WAJIB DITAMBAHKAN UNTUK REAL-TIME

#[Layout('layouts.customer')]
#[Title('Pesanan Saya')]
class ActiveOrder extends Component
{
    public $customer;
    public $tableSession;
    public $table;

    public function mount()
    {
        if (!session()->has('customer_id')) {
            return redirect()->route('home');
        }

        $this->customer = SessionCustomer::with('tableSession.table')->find(session('customer_id'));

        if (!$this->customer || $this->customer->tableSession->status !== 'active') {
            session()->forget('customer_id');
            session()->forget('cart');
            return redirect()->route('home');
        }

        $this->tableSession = $this->customer->tableSession;
        $this->table = $this->tableSession->table;
    }

    #[On('echo:customer-table-{tableSession.id},.order.placed')]
    #[On('echo:customer-table-{tableSession.id},.order.updated')]
    public function refreshOrderStatus()
    {
        // Biarkan kosong. Livewire otomatis memuat ulang fungsi render()
        // sehingga status Dimasak/Selesai akan langsung berubah di layar pelanggan!
    }
    public function render()
    {
        $orders = Order::with(['items.product', 'items.customer', 'items.selectedAddons'])
            ->where('table_session_id', $this->tableSession->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $setting = Setting::first();
        $taxPercentage = $setting->tax_percentage ?? 0;

        $personSubtotals = [];
        $realSubtotal = 0;

        foreach ($orders as $order) {
            if ($order->payment_status === 'unpaid') {
                foreach ($order->items as $item) {
                    $name = $item->customer->display_name ?? 'Tamu';
                    if (!isset($personSubtotals[$name])) {
                        $personSubtotals[$name] = 0;
                    }

                    $itemFinalPrice = $item->price_at_order;
                    foreach ($item->selectedAddons as $addon) {
                        $itemFinalPrice += $addon->addon_price;
                    }

                    $itemTotalCost = ($itemFinalPrice * $item->qty);
                    $personSubtotals[$name] += $itemTotalCost;
                    $realSubtotal += $itemTotalCost;
                }
            }
        }

        $taxAmount = $realSubtotal * ($taxPercentage / 100);
        $totalBill = $realSubtotal + $taxAmount;

        $personDetails = [];
        foreach ($personSubtotals as $name => $rawTotal) {
            $personTax = $rawTotal * ($taxPercentage / 100);
            $personDetails[$name] = [
                'subtotal' => $rawTotal,
                'tax' => $personTax,
                'total' => $rawTotal + $personTax
            ];
        }

        return view('livewire.customer.active-order', [
            'orders' => $orders,
            'subtotal' => $realSubtotal,
            'taxPercentage' => $taxPercentage,
            'taxAmount' => $taxAmount,
            'totalBill' => $totalBill,
            'personDetails' => $personDetails,
            'setting' => $setting
        ]);
    }
}
