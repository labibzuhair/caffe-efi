<?php

namespace App\Livewire\Customer;

use App\Models\Product;
use App\Models\Setting;
use App\Models\SessionCustomer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\On;

class FloatingCart extends Component
{
    public $cart = [];
    public $isOpen = false;
    public $taxPercentage = 0;

    public function mount()
    {
        $this->cart = session()->get('cart', []);
        $setting = Setting::first();
        $this->taxPercentage = $setting->tax_percentage ?? 0;
    }

    // FUNGSI BARU: Mengambil data keranjang dan melemparnya ke Pop-Up
    public function editItem($customerId, $cartKey)
    {
        if (isset($this->cart[$customerId]['items'][$cartKey])) {
            $item = $this->cart[$customerId]['items'][$cartKey];
            $product = Product::with('addons')->find($item['product_id']);

            if ($product) {
                $this->dispatch('open-product-modal', [
                    'product' => $product->toArray(),
                    'customerId' => $customerId,
                    'qty' => $item['qty'],
                    'selectedAddonIds' => array_column($item['addons'], 'id'),
                    'note' => $item['note'] ?? '',
                    'oldCartKey' => $cartKey // Penanda bahwa ini adalah proses "Edit"
                ]);
            }
        }
    }

    #[On('add-to-cart')]
    public function addToCart($productId, $customerId, $qty = 1, $addons = [], $note = '', $oldCartKey = null)
    {
        $product = Product::find($productId);
        $customer = SessionCustomer::find($customerId);

        if (!$product || !$product->is_active || !$customer)
            return;

        // JIKA EDIT: Hapus data lama dari keranjang
        if ($oldCartKey && isset($this->cart[$customerId]['items'][$oldCartKey])) {
            unset($this->cart[$customerId]['items'][$oldCartKey]);
        }

        // BIKIN KUNCI UNIK: ID + ID Varian + Hash Catatan
        $addonIds = array_column($addons, 'id');
        sort($addonIds);
        $safeNote = trim($note);
        $cartKey = $productId . '_' . implode('_', $addonIds) . '_' . md5($safeNote);

        if (!isset($this->cart[$customerId])) {
            $this->cart[$customerId] = ['name' => $customer->display_name, 'items' => []];
        }

        if (isset($this->cart[$customerId]['items'][$cartKey])) {
            $this->cart[$customerId]['items'][$cartKey]['qty'] += $qty;
        } else {
            $totalAddonPrice = 0;
            $addonDetails = [];

            // Mengambil langsung harga matang yang dikirim dari Pop-up Frontend! Anti-NaN!
            foreach ($addons as $ad) {
                $adPrice = floatval($ad['price'] ?? 0);
                $adCogs = floatval($ad['cogs'] ?? 0);

                $totalAddonPrice += $adPrice;
                $addonDetails[] = [
                    'id' => $ad['id'],
                    'name' => $ad['name'],
                    'price' => $adPrice,
                    'cogs' => $adCogs
                ];
            }

            // Fallback cari harga dasar dari model
            $pPrice = $product->price ?? $product->harga ?? 0;
            $pCogs = $product->cogs ?? $product->modal ?? 0;

            $this->cart[$customerId]['items'][$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'base_price' => floatval($pPrice),
                'cogs' => floatval($pCogs),
                'final_price' => floatval($pPrice) + $totalAddonPrice,
                'qty' => $qty,
                'image' => $product->image,
                'addons' => $addonDetails,
                'note' => $safeNote
            ];
        }

        $this->saveCart();
    }

    public function increase($customerId, $cartKey)
    {
        if (isset($this->cart[$customerId]['items'][$cartKey])) {
            $this->cart[$customerId]['items'][$cartKey]['qty']++;
            $this->saveCart();
        }
    }

    public function decrease($customerId, $cartKey)
    {
        if (isset($this->cart[$customerId]['items'][$cartKey])) {
            if ($this->cart[$customerId]['items'][$cartKey]['qty'] > 1) {
                $this->cart[$customerId]['items'][$cartKey]['qty']--;
            } else {
                unset($this->cart[$customerId]['items'][$cartKey]);
                if (empty($this->cart[$customerId]['items']))
                    unset($this->cart[$customerId]);
            }
            $this->saveCart();
        }
    }

    public function remove($customerId, $cartKey)
    {
        if (isset($this->cart[$customerId]['items'][$cartKey])) {
            unset($this->cart[$customerId]['items'][$cartKey]);
            if (empty($this->cart[$customerId]['items']))
                unset($this->cart[$customerId]);
            $this->saveCart();
        }
    }

    private function saveCart()
    {
        session()->put('cart', $this->cart);
    }

    public function getSubtotalProperty()
    {
        $total = 0;
        foreach ($this->cart as $customerData) {
            foreach ($customerData['items'] as $item) {
                $total += ($item['final_price'] ?? 0) * ($item['qty'] ?? 1);
            }
        }
        return $total;
    }

    public function getTaxAmountProperty()
    {
        return $this->subtotal * ($this->taxPercentage / 100);
    }
    public function getTotalProperty()
    {
        return $this->subtotal + $this->taxAmount;
    }
    public function getTotalItemsProperty()
    {
        $count = 0;
        foreach ($this->cart as $customerData) {
            foreach ($customerData['items'] as $item) {
                $count += ($item['qty'] ?? 1);
            }
        }
        return $count;
    }

    public function checkout()
    {
        if (empty($this->cart))
            return;

        $hostCustomerId = session('customer_id');
        $hostCustomer = SessionCustomer::find($hostCustomerId);

        if (!$hostCustomer || $hostCustomer->tableSession->status !== 'active') {
            return redirect()->route('home');
        }

        try {
            DB::beginTransaction();

            // ==============================================================
            // PERUBAHAN UTAMA: GABUNGKAN ORDER JIKA BELUM BAYAR
            // ==============================================================

            // 1. Cari apakah ada order 'unpaid' (belum lunas) di sesi meja ini
            $order = Order::where('table_session_id', $hostCustomer->table_session_id)
                ->where('payment_status', 'unpaid')
                ->first();

            // 2. Jika tidak ada, buat Order Baru
            if (!$order) {
                $order = Order::create([
                    'table_session_id' => $hostCustomer->table_session_id,
                    'order_number' => 'ORD-' . date('ymd') . '-' . strtoupper(Str::random(5)),
                    'total_price' => 0, // Akan diupdate di bawah
                    'status' => 'pending',
                    'payment_status' => 'unpaid'
                ]);
            }

            // 3. Masukkan item baru dari keranjang
            $cartTotal = 0; // Untuk menghitung tambahan harga
            foreach ($this->cart as $customerId => $customerData) {
                foreach ($customerData['items'] as $cartKey => $item) {

                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'session_customer_id' => $customerId,
                        'qty' => $item['qty'] ?? 1,
                        'price_at_order' => $item['base_price'] ?? 0,
                        'cogs_at_order' => $item['cogs'] ?? 0,
                        'notes' => $item['note'] ?? null,
                        'status' => 'pending'
                    ]);

                    $itemTotal = ($item['base_price'] ?? 0) * ($item['qty'] ?? 1);

                    if (!empty($item['addons'])) {
                        foreach ($item['addons'] as $addon) {
                            OrderItemAddon::create([
                                'order_item_id' => $orderItem->id,
                                'product_addon_id' => $addon['id'],
                                'addon_name' => $addon['name'],
                                'addon_price' => $addon['price'] ?? 0,
                                'addon_cogs' => $addon['cogs'] ?? 0
                            ]);
                            $itemTotal += ($addon['price'] ?? 0) * ($item['qty'] ?? 1);
                        }
                    }

                    $cartTotal += $itemTotal;
                }
            }

            // 4. Update Total Harga Order (Harga Lama + Harga Tambahan Baru)
            $order->increment('total_price', $cartTotal);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error_order', 'Terjadi kesalahan teknis. Silakan coba lagi.');
            return;
        }

        $this->cart = [];
        session()->forget('cart');
        $this->isOpen = false;

        // Gunakan parameter khusus jika ini pesanan tambahan
        try {
            event(new \App\Events\OrderPlaced($order));
        } catch (\Exception $e) {
        }

        session()->flash('success_order', 'Pesanan berhasil dikirim ke Dapur!');
        return $this->redirect(route('customer.active-order'), navigate: true);
    }
    public function render()
    {
        return view('livewire.customer.floating-cart');
    }
}
