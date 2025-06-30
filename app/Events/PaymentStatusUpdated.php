<?php

namespace App\Events;

use App\Models\MpesaPayment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class PaymentStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(public MpesaPayment $payment) {}

    /**
     * Channel name: private.payments.{id}
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('payments.' . $this->payment->id);
    }

    /**
     * Payload sent to Echo / Pusher
     */
    public function broadcastWith(): array
    {
        return [
            'id'        => $this->payment->id,
            'status'    => $this->payment->status,
            'reason'    => $this->payment->reason,
            'updated_at' => $this->payment->updated_at->toIso8601String(),
        ];
    }

    /**
     * Optional: rename the JS event to something nice
     */
    public function broadcastAs(): string
    {
        return 'PaymentStatusUpdated';
    }
}
