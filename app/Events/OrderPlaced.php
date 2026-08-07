<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // KUNCI UTAMA: Gunakan ShouldBroadcastNow!
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    // Saluran publik agar Dapur bisa mendengarnya tanpa Auth rumit
    public function broadcastOn(): array
    {
        return [
            new Channel('kitchen-channel'),
        ];
    }

    // Nama panggilan event ini di frontend
    public function broadcastAs(): string
    {
        return 'order.placed';
    }
}
