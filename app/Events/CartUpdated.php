<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CartUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tableSessionId;

    public function __construct($tableSessionId)
    {
        $this->tableSessionId = $tableSessionId;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('customer-table-' . $this->tableSessionId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'cart.updated';
    }
}
