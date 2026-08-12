<?php

namespace App\Livewire\Customer;

use App\Models\Product;
use App\Models\Setting;
use App\Models\SessionCustomer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;
use App\Models\CartItem; // Panggil model CartItem baru kita
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\On;

class FloatingCart extends Component
{
    public $cart = [];
    public $isOpen = false;

    // Properti hitung-hitungan
    public $subtotal = 0;
    public $taxAmount = 0;
    public $total = 0;
    public $totalItems = 0;
    public $taxPercentage = 0;

    // KUNCI UTAMA WEBSOCKET: public property ini wajib ada agar bisa di-bind ke #[On]
    public $tableSessionId;

    public function mount()
    {
        $setting = Setting::first();
        $this->taxPercentage = $setting->tax_percentage ?? 0;

        if (session()->has('customer_id')) {
            $customer = SessionCustomer::find(session('customer_id'));
            if ($customer) {
                $this->tableSessionId = $customer->table_session_id;
                $this->loadCart(); // Pemuatan awal dari database
            }
        }
    }

    // =================================================================
    // LISTENER WEBSOCKET: Memperbarui keranjang saat teman meja mengubahnya
    // =================================================================
    #[On('refreshCart')]
    public function refreshCart()
    {
        $this->loadCart();
    }

    // FUNGSI INTI: Membaca tabel 'cart_items' dan merapikannya untuk UI
    public function loadCart()
    {
        if (!$this->tableSessionId)
            return;

        // Ambil semua isi keranjang dari database beserta data customer dan produknya
        $items = CartItem::with(['customer', 'product'])
            ->where('table_session_id', $this->tableSessionId)
            ->get();

        $groupedCart = [];
        $tempSubtotal = 0;
        $tempTotalItems = 0;

        foreach ($items as $item) {
            $customerId = $item->session_customer_id;

            // Kelompokkan berdasarkan ID Customer
            if (!isset($groupedCart[$customerId])) {
                $groupedCart[$customerId] = [
                    'name' => $item->customer->display_name ?? 'Tamu',
                    'items' => []
                ];
            }

            // Hitung harga final (Harga dasar + harga addon)
            $finalPrice = floatval($item->base_price);
            if (!empty($item->addons)) {
                foreach ($item->addons as $addon) {
                    $finalPrice += floatval($addon['price'] ?? 0);
                }
            }

            $groupedCart[$customerId]['items'][$item->id] = [
                'id' => $item->id, // Kita pakai ID dari database sekarang!
                'product_id' => $item->product_id,
                'name' => $item->product->name ?? 'Menu Dihapus',
                'base_price' => floatval($item->base_price),
                'cogs' => floatval($item->cogs),
                'final_price' => $finalPrice,
                'qty' => $item->qty,
                'image' => $item->product->image ?? null,
                'addons' => $item->addons ?? [],
                'note' => $item->note
            ];

            $tempSubtotal += ($finalPrice * $item->qty);
            $tempTotalItems += $item->qty;
        }

        $this->cart = $groupedCart;
        $this->subtotal = $tempSubtotal;
        $this->taxAmount = $tempSubtotal * ($this->taxPercentage / 100);
        $this->total = $this->subtotal + $this->taxAmount;
        $this->totalItems = $tempTotalItems;
    }

    // MENGEDIT MENU YANG ADA DI KERANJANG
    public function editItem($cartItemId)
    {
        $item = CartItem::with('product.addons')->find($cartItemId);
        if ($item && $item->product) {
            $this->dispatch('open-product-modal', [
                'product' => $item->product->toArray(),
                'customerId' => $item->session_customer_id,
                'qty' => $item->qty,
                'selectedAddonIds' => array_column($item->addons ?? [], 'id'),
                'note' => $item->note ?? '',
                'oldCartKey' => $item->id // Kirim ID database ke modal
            ]);
        }
    }

    // MENAMBAHKAN / MENGUPDATE MENU KE DATABASE
    #[On('add-to-cart')]
    public function addToCart($productId, $customerId, $qty = 1, $addons = [], $note = '', $oldCartItemId = null)
    {
        $product = Product::find($productId);
        if (!$product || !$product->is_active || !$this->tableSessionId)
            return;

        // JIKA EDIT: Hapus data lama dari database
        if ($oldCartItemId) {
            CartItem::where('id', $oldCartItemId)->where('table_session_id', $this->tableSessionId)->delete();
        }

        // Siapkan struktur addons
        $addonDetails = [];
        foreach ($addons as $ad) {
            $addonDetails[] = [
                'id' => $ad['id'],
                'name' => $ad['name'],
                'price' => floatval($ad['price'] ?? 0),
                'cogs' => floatval($ad['cogs'] ?? 0)
            ];
        }

        // Cek apakah persis ada menu yang sama (produk, customer, catatan) di keranjang
        $safeNote = trim($note);
        $existingItem = CartItem::where('table_session_id', $this->tableSessionId)
            ->where('session_customer_id', $customerId)
            ->where('product_id', $productId)
            ->where('note', $safeNote)
            ->get()
            ->first(function ($item) use ($addonDetails) {
                // Bandingkan ID addon (pastikan isinya sama)
                $dbAddonIds = collect($item->addons)->pluck('id')->sort()->values()->toArray();
                $newAddonIds = collect($addonDetails)->pluck('id')->sort()->values()->toArray();
                return $dbAddonIds === $newAddonIds;
            });

        if ($existingItem) {
            $existingItem->increment('qty', $qty);
        } else {
            CartItem::create([
                'table_session_id' => $this->tableSessionId,
                'session_customer_id' => $customerId,
                'product_id' => $product->id,
                'qty' => $qty,
                'base_price' => floatval($product->price ?? 0),
                'cogs' => floatval($product->cogs ?? 0),
                'addons' => empty($addonDetails) ? null : $addonDetails,
                'note' => $safeNote
            ]);
        }

        $this->loadCart();
        event(new \App\Events\CartUpdated($this->tableSessionId)); // Sebarkan sinyal ke HP teman!
    }

    public function increase($cartItemId)
    {
        $item = CartItem::where('id', $cartItemId)->where('table_session_id', $this->tableSessionId)->first();
        if ($item) {
            $item->increment('qty');
            $this->loadCart();
            event(new \App\Events\CartUpdated($this->tableSessionId));
        }
    }

    public function decrease($cartItemId)
    {
        $item = CartItem::where('id', $cartItemId)->where('table_session_id', $this->tableSessionId)->first();
        if ($item) {
            if ($item->qty > 1) {
                $item->decrement('qty');
            } else {
                $item->delete();
            }
            $this->loadCart();
            event(new \App\Events\CartUpdated($this->tableSessionId));
        }
    }

    public function remove($cartItemId)
    {
        CartItem::where('id', $cartItemId)->where('table_session_id', $this->tableSessionId)->delete();
        $this->loadCart();
        event(new \App\Events\CartUpdated($this->tableSessionId));
    }

    public function checkout()
    {
        if (!$this->tableSessionId || count($this->cart) === 0)
            return;

        // Ambil SEMUA keranjang milik meja ini langsung dari database
        $cartItems = CartItem::where('table_session_id', $this->tableSessionId)->get();
        if ($cartItems->isEmpty())
            return;

        try {
            DB::beginTransaction();

            $order = Order::where('table_session_id', $this->tableSessionId)
                ->where('payment_status', 'unpaid')
                ->first();

            if (!$order) {
                $order = Order::create([
                    'table_session_id' => $this->tableSessionId,
                    'order_number' => 'ORD-' . date('ymd') . '-' . strtoupper(Str::random(5)),
                    'total_price' => 0,
                    'status' => 'pending',
                    'payment_status' => 'unpaid'
                ]);
            }

            $cartTotal = 0;

            // Pindahkan data dari CartItem -> OrderItem
            foreach ($cartItems as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'session_customer_id' => $item->session_customer_id,
                    'qty' => $item->qty,
                    'price_at_order' => $item->base_price,
                    'cogs_at_order' => $item->cogs,
                    'notes' => $item->note,
                    'status' => 'pending'
                ]);

                $itemTotal = floatval($item->base_price) * $item->qty;

                if (!empty($item->addons)) {
                    foreach ($item->addons as $addon) {
                        OrderItemAddon::create([
                            'order_item_id' => $orderItem->id,
                            'product_addon_id' => $addon['id'],
                            'addon_name' => $addon['name'],
                            'addon_price' => floatval($addon['price'] ?? 0),
                            'addon_cogs' => floatval($addon['cogs'] ?? 0)
                        ]);
                        $itemTotal += floatval($addon['price'] ?? 0) * $item->qty;
                    }
                }

                $cartTotal += $itemTotal;
            }

            $order->increment('total_price', $cartTotal);

            // KOSONGKAN KERANJANG DATABASE
            CartItem::where('table_session_id', $this->tableSessionId)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error_order', 'Terjadi kesalahan teknis. Silakan coba lagi.');
            return;
        }

        $this->isOpen = false;

        // Beritahu dapur ada pesanan (Bunyikan lonceng dapur)
        try {
            event(new \App\Events\OrderPlaced($order));

            // Beritahu HP teman di meja bahwa keranjang sudah dicheckout dan tagihan baru dibuat
            event(new \App\Events\CartUpdated($this->tableSessionId));
            event(new \App\Events\OrderUpdated($this->tableSessionId));
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
