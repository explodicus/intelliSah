<?php

namespace App\Jobs;

use App\Entities\GameSubscription;
use App\Events\GameEvent;
use App\Providers\GameProvider;
use App\Providers\Helper\AIHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BotAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var GameSubscription
     */
    protected $subscription;

    /**
     * Create a new job instance.
     *
     * @param GameSubscription $subscription
     */
    public function __construct(GameSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @param GameProvider $gameProvider
     * @param AIHandler $aiHandler
     */
    public function handle(GameProvider $gameProvider, AIHandler $aiHandler)
    {
        $session = $this->subscription->session;
        if ($session->current_subscription_id !== $this->subscription->id) {
            return;
        }

        $nextQuery = $aiHandler->nextQuery($session);
//        if (!$nextQuery) {
//            throw new \Exception("AI can't handle session {$session->id} for bot {$subscription->user->id}");
//        }

        list ($positionFrom, $positionTo) = $nextQuery;
        if (!$gameProvider->performAction($session, $positionFrom, $positionTo)) {
            throw new \Exception("Unexpected position");
        }

        $failedSubscriptions = $session->fail_subscriptions ?: [];
        if (count($failedSubscriptions) === 3) {
            $session->gameover = true;
            $session->save();

            broadcast(new GameEvent($session));
            return;
        }

        $currentSubscription = GameSubscription::find($session->current_subscription_id);
        $currentSide = $currentSubscription->side;
        while (true) {
            $currentSide += 1;
            if ($currentSide > 4) {
                $currentSide = 1;
            }

            $excludedSide = array_filter($failedSubscriptions, function ($subscription) use ($currentSide) {
                if ($subscription['side'] === $currentSide) {
                    return true;
                }

                return false;
            });

            if ($excludedSide) {
                continue;
            }

            break;
        }

        foreach ($session->subscriptions as $subscription) {
            if ($subscription->side === $currentSide) {
                $session->current_subscription_id = $subscription->id;
                break;
            }
        }
        $session->save();

        broadcast(new GameEvent($session));
    }
}
