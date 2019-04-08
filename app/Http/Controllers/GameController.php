<?php

namespace App\Http\Controllers;

use App\Entities\GameSession;
use App\Entities\GameSubscription;
use App\Events\GameEvent;
use App\Providers\GameProvider;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * @var GameProvider
     */
    protected $gameProvider;

    /**
     * GameController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->gameProvider = new GameProvider();
    }

    /**
     * @param Request $request
     * @param GameSession $session
     */
    public function handle(Request $request, GameSession $session)
    {
        $positionFrom = $request->position_from;
        $positionTo = $request->position_to;

        if (!$this->gameProvider->performAction($session, $positionFrom, $positionTo)) {
            return;
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
