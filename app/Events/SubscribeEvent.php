<?php

namespace App\Events;

use App\Entities\GameSession;
use App\Entities\GameSubscription;
use App\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SubscribeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var GameSubscription
     */
    public $subscription;

    /**
     * @var GameSession
     */
    public $session;

    /**
     * SubscribeEvent constructor.
     * @param GameSubscription $subscription
     * @param GameSession $session
     */
    public function __construct(GameSubscription $subscription, GameSession $session)
    {
        $this->subscription = $subscription;
        $this->session = $session;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("sah.subscriber.{$this->session->id}");
    }
}
