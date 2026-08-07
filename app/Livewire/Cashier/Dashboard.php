<?php

namespace App\Livewire\Cashier;

use App\Models\Table;
use App\Models\TableSession;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
#[Title('Dasbor Kasir')]
class Dashboard extends Component
{
    public $selectedSession = null;
    public $paymentModalOpen = false;

    public $newProductId = '';
    public $newQty = 1;
    public $newCustomerId = '';
    public $newNotes = '';
    public $newAddons = [];

    public $paymentMethod = 'Cash';
    public $cashReceived = '';
    public $changeAmount = 0;

    public $callModalOpen = false;
    public $callMessage = '';
    public $callSessionId = null;

    public $searchTable = '';
    public $searchProduct = '';
    public $filterCategory = '';
    public $isPriceModalOpen = false;
    public $searchCatalog = '';

    #[On('echo:cashier-channel,.App\\Events\\OrderReadyForPickup')]
    public function handleKitchenNotification($payload)
    {
        $this->dispatch('play-cashier-bell');
    }

    #[On('echo:kitchen-channel,.order.placed')]
    public function handleRealtimeUpdates()
    {
        if ($this->paymentModalOpen && $this->selectedSession) {
            $this->selectedSession = TableSession::with(['table', 'customers', 'orders.items.customer', 'orders.items.product', 'orders.items.selectedAddons'])
                ->find($this->selectedSession->id);
        }
    }

    public function markAsServed($itemId)
    {
        $item = OrderItem::find($itemId);
        if ($item) {
            $item->update(['status' => 'served']);
        }
    }

    public function openPriceModal()
    {
        $this->searchCatalog = '';
        $this->isPriceModalOpen = true;
    }
    public function closePriceModal()
    {
        $this->isPriceModalOpen = false;
        $this->searchCatalog = '';
    }
    public function openCallModal($sessionId, $tableNumber)
    {
        $this->callSessionId = $sessionId;
        $this->callMessage = "Pesanan Meja {$tableNumber} sudah siap. Silakan ambil di kasir!";
        $this->callModalOpen = true;
    }
    public function closeCallModal()
    {
        $this->callModalOpen = false;
        $this->callMessage = '';
        $this->callSessionId = null;
    }
    public function sendCallToCustomer()
    {
        if ($this->callSessionId && !empty($this->callMessage)) {
            try {
                event(new \App\Events\CallCustomer($this->callSessionId, $this->callMessage));
                session()->flash('success', 'Panggilan berhasil dikirim ke HP Pelanggan!');
            } catch (\Exception $e) {
                session()->flash('error', 'Gagal mengirim panggilan.');
            }
        }
        $this->closeCallModal();
    }
    public function openPaymentModal($sessionId)
    {
        $this->selectedSession = TableSession::with(['table', 'customers', 'orders.items.customer', 'orders.items.product', 'orders.items.selectedAddons'])->find($sessionId);
        if ($this->selectedSession->customers->count() > 0)
            $this->newCustomerId = $this->selectedSession->customers->first()->id;
        $this->resetAddItemForm();
        $this->paymentMethod = 'Cash';
        $this->cashReceived = '';
        $this->changeAmount = 0;
        $this->paymentModalOpen = true;
    }
    public function closePaymentModal()
    {
        $this->paymentModalOpen = false;
        $this->selectedSession = null;
    }
    public function updatedCashReceived()
    {
        if ($this->paymentMethod !== 'Cash') {
            $this->changeAmount = 0;
            return;
        }
        $totalTagihan = $this->calculateTotalForSelectedSession();
        $uangDiterima = (int) preg_replace('/[^0-9]/', '', $this->cashReceived);
        $this->changeAmount = max(0, $uangDiterima - $totalTagihan);
    }
    public function updatedPaymentMethod()
    {
        $this->cashReceived = '';
        $this->changeAmount = 0;
    }
    public function updatedNewProductId()
    {
        $this->newAddons = [];
        $this->newNotes = '';
    }
    private function resetAddItemForm()
    {
        $this->searchProduct = '';
        $this->filterCategory = '';
        $this->newProductId = '';
        $this->newQty = 1;
        $this->newNotes = '';
        $this->newAddons = [];
    }

    private function calculateTotalForSelectedSession()
    {
        if (!$this->selectedSession)
            return 0;
        $setting = Setting::first();
        $taxPercentage = $setting->tax_percentage ?? 0;
        $modalSubtotal = 0;

        foreach ($this->selectedSession->orders->where('payment_status', 'unpaid') as $order) {
            foreach ($order->items as $item) {
                $itemPrice = $item->price_at_order;
                foreach ($item->selectedAddons as $addon) {
                    $itemPrice += $addon->addon_price;
                }
                $modalSubtotal += $itemPrice * $item->qty;
            }
        }
        return $modalSubtotal + ($modalSubtotal * ($taxPercentage / 100));
    }

    public function cancelItem($itemId)
    {
        $item = OrderItem::with('selectedAddons')->find($itemId);
        if ($item && $item->status === 'pending') {
            $order = $item->order;
            $itemPrice = $item->price_at_order;
            foreach ($item->selectedAddons as $addon) {
                $itemPrice += $addon->addon_price;
            }
            $priceToDeduct = $itemPrice * $item->qty;
            $item->delete();
            $order->update(['total_price' => max(0, $order->total_price - $priceToDeduct)]);
            $this->openPaymentModal($this->selectedSession->id);
            session()->flash('success_cancel', 'Menu berhasil dibatalkan karena belum diproses dapur.');
        } else {
            session()->flash('error_cancel', 'Gagal! Menu ini sedang atau sudah selesai dimasak.');
        }
    }
    public function addItem()
    {
        if (!$this->newProductId || !$this->newCustomerId || $this->newQty < 1) {
            session()->flash('error_add', 'Pilih menu, pemesan, dan jumlah yang valid.');
            return;
        }
        $product = Product::find($this->newProductId);
        if (!$product)
            return;
        try {
            DB::beginTransaction();
            $order = Order::where('table_session_id', $this->selectedSession->id)->where('payment_status', 'unpaid')->first();
            if (!$order) {
                $order = Order::create(['table_session_id' => $this->selectedSession->id, 'order_number' => 'ORD-' . date('ymd') . '-' . strtoupper(Str::random(5)), 'total_price' => 0, 'status' => 'pending', 'payment_status' => 'unpaid']);
            }
            $orderItem = OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'session_customer_id' => $this->newCustomerId, 'qty' => $this->newQty, 'price_at_order' => $product->price, 'cogs_at_order' => $product->cogs ?? 0, 'notes' => trim($this->newNotes), 'status' => 'pending']);
            $totalAddonPrice = 0;
            if (!empty($this->newAddons)) {
                $selectedAddons = ProductAddon::whereIn('id', $this->newAddons)->get();
                foreach ($selectedAddons as $addon) {
                    $addonPrice = floatval($addon->additional_price ?? 0);
                    OrderItemAddon::create(['order_item_id' => $orderItem->id, 'product_addon_id' => $addon->id, 'addon_name' => $addon->name, 'addon_price' => $addonPrice, 'addon_cogs' => floatval($addon->additional_cogs ?? 0)]);
                    $totalAddonPrice += $addonPrice;
                }
            }
            $priceToAdd = ($product->price + $totalAddonPrice) * $this->newQty;
            $order->increment('total_price', $priceToAdd);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error_add', 'Gagal menyimpan pesanan.');
            return;
        }
        try {
            event(new \App\Events\OrderPlaced($order->load(['items.product', 'items.customer', 'items.selectedAddons'])));
        } catch (\Exception $e) {
        }
        $this->resetAddItemForm();
        $this->openPaymentModal($this->selectedSession->id);
        session()->flash('success_add', 'Menu susulan berhasil dikirim ke Dapur!');
    }

    public function processPayment()
    {
        if (!$this->selectedSession)
            return;
        $totalTagihan = $this->calculateTotalForSelectedSession();
        $uangDiterima = (int) preg_replace('/[^0-9]/', '', $this->cashReceived);
        if ($this->paymentMethod === 'Cash' && $uangDiterima < $totalTagihan) {
            session()->flash('error_payment', 'Nominal uang tidak cukup!');
            return;
        }
        try {
            DB::beginTransaction();

            $unpaidOrder = Order::where('table_session_id', $this->selectedSession->id)
                ->where('payment_status', 'unpaid')
                ->first();

            if ($unpaidOrder) {
                $unpaidOrder->update([
                    'payment_status' => 'paid',
                    'payment_method' => $this->paymentMethod,
                    'cash_received' => $this->paymentMethod === 'Cash' ? $uangDiterima : null,
                    'change_amount' => $this->paymentMethod === 'Cash' ? $this->changeAmount : null
                ]);
            }

            $table = Table::find($this->selectedSession->table_id);
            if ($table) {
                $table->update(['status' => 'dirty']);
            }
            DB::commit();

            if ($unpaidOrder) {
                $this->dispatch('print-receipt', url: route('cetak.struk', $unpaidOrder->id));
            }

            $this->closePaymentModal();
            session()->flash('success', 'Pembayaran Lunas! Meja ' . $table->table_number . ' berstatus Kotor/Lunas.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function clearTable($tableId, $sessionId)
    {
        try {
            DB::beginTransaction();
            TableSession::where('id', $sessionId)->update(['status' => 'completed']);
            Table::where('id', $tableId)->update(['status' => 'available']);
            DB::commit();
            session()->flash('success', 'Sesi diakhiri, meja dibersihkan dan siap digunakan pelanggan baru.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal membersihkan meja.');
        }
    }

    public function render()
    {
        $tablesQuery = Table::with([
            'sessions' => function ($q) {
                $q->where('status', 'active')->with(['orders.items.selectedAddons', 'customers']);
            }
        ]);

        if (!empty($this->searchTable)) {
            $tablesQuery->where('table_number', 'like', '%' . $this->searchTable . '%');
        }

        $tables = $tablesQuery->get()->sortByDesc(function ($table) {
            if ($table->sessions->isEmpty())
                return 0;
            return $table->sessions->first()->created_at->timestamp;
        });

        $productsQuery = Product::with('addons')->where('is_active', true);
        if (!empty($this->searchProduct))
            $productsQuery->where('name', 'like', '%' . $this->searchProduct . '%');
        if (!empty($this->filterCategory))
            $productsQuery->where('category_id', $this->filterCategory);
        $availableProducts = $productsQuery->get();

        $categories = Category::all();
        $setting = Setting::first();
        $taxPercentage = $setting->tax_percentage ?? 0;

        return view('livewire.cashier.dashboard', [
            'tables' => $tables,
            'availableProducts' => $availableProducts,
            'categories' => $categories,
            'taxPercentage' => $taxPercentage
        ]);
    }
}
