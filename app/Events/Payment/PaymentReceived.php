<?php

namespace App\Events\Payment;

use App\Events\Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * When a new payment into member deposit account has been made.
 *
 * - attach listener to repay any pending loan amount
 *
 * Class PaymentReceived
 * @package App\Events\Payment
 */
class PaymentReceived extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $memberId;

    /**
     * PayLoan constructor.
     * @param $memberId
     */
    public function __construct($memberId)
    {
        $this->memberId = $memberId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
