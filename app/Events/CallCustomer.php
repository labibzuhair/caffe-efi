<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallCustomer implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tableSessionId;
    public $message; // Pesan khusus dari kasir untuk pelanggan

    /**
     * Create a new event instance.
     */
    public function __construct($tableSessionId, $message)
    {
        $this->tableSessionId = $tableSessionId;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast ke channel spesifik untuk meja tersebut saja
        // Agar HP di meja lain tidak ikut berbunyi!
        return [
            new Channel('customer-table-' . $this->tableSessionId),
        ];
    }
}
