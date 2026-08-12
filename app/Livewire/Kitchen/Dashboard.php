<?php

namespace App\Livewire\Kitchen;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
#[Title('Layar Dapur (KDS)')]
class Dashboard extends Component
{
    public $searchTable = '';
    public $searchProduct = '';
    public $filterCategory = '';

    public function updateItemStatus($itemId, $newStatus)
    {
        $item = OrderItem::find($itemId);
        if ($item) {
            $item->update(['status' => $newStatus]);
            $this->checkAndCompleteOrder($item->order_id);

            // ==========================================
            // PERBAIKAN: Beritahu HANYA meja yang bersangkutan
            // ==========================================
            try {
                // Hapus ini: event(new \App\Events\OrderPlaced($item->order));
                // Ganti menjadi ini:
                event(new \App\Events\OrderUpdated($item->order->table_session_id));
            } catch (\Exception $e) {
            }
        }
    }

    public function callCashier($orderId)
    {
        $order = Order::with(['items.product', 'session.table'])->find($orderId);
        if (!$order)
            return;

        // Cari HANYA item yang berstatus ready_to_serve (hijau di layar koki)
        $readyItems = $order->items->where('status', 'ready_to_serve');

        if ($readyItems->isEmpty())
            return;

        $summary = [];
        foreach ($readyItems as $item) {
            $summary[] = $item->qty . 'x ' . $item->product->name;
        }
        $menuList = implode(', ', $summary);
        $tableName = $order->session->table->table_number ?? 'Takeaway';

        $message = "Pesanan {$tableName} siap diambil: {$menuList}.";

        // Ubah status menjadi waiting_pickup agar HILANG dari Dapur dan MASUK ke Lonceng Kasir
        foreach ($readyItems as $item) {
            $item->update(['status' => 'waiting_pickup']);
        }

        $this->checkAndCompleteOrder($orderId);

        try {
            event(new \App\Events\OrderReadyForPickup($order, $message));
            event(new \App\Events\OrderUpdated($order->table_session_id));
        } catch (\Exception $e) {
        }

        session()->flash('success_call', "Panggilan untuk {$tableName} berhasil dikirim ke Kasir!");
    }

    private function checkAndCompleteOrder($orderId)
    {
        $order = Order::with('items')->find($orderId);
        $hasUnfinishedItems = $order->items->whereIn('status', ['pending', 'cooking'])->count() > 0;

        if (!$hasUnfinishedItems) {
            $order->update(['status' => 'completed']);
        } else {
            $order->update(['status' => 'processing']);
        }
    }

    #[On('echo:kitchen-channel,OrderPlaced')]
    public function refreshOrders($payload = null)
    {
        $this->dispatch('play-kitchen-bell');
    }

    public function render()
    {
        // Pastikan Dapur hanya melihat: pending, cooking, dan ready_to_serve
        $itemConstraints = function ($query) {
            $query->whereIn('status', ['pending', 'cooking', 'ready_to_serve']);

            if (!empty($this->searchProduct)) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchProduct . '%');
                });
            }

            if (!empty($this->filterCategory)) {
                $query->whereHas('product', function ($q) {
                    $q->where('category_id', $this->filterCategory);
                });
            }
        };

        $orders = Order::with(['session.table', 'items' => $itemConstraints, 'items.product', 'items.customer', 'items.selectedAddons'])
            ->whereHas('items', $itemConstraints)
            ->when(!empty($this->searchTable), function ($query) {
                $query->whereHas('session.table', function ($q) {
                    $q->where('table_number', 'like', '%' . $this->searchTable . '%');
                });
            })
            ->orderBy('created_at', 'asc') // FIFO
            ->get();

        $categories = Category::all();

        return view('livewire.kitchen.dashboard', [
            'orders' => $orders,
            'categories' => $categories
        ]);
    }
}
