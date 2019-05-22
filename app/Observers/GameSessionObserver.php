<?php


namespace App\Observers;


use App\Entities\GameSession;
use App\Entities\GameSubscription;
use App\Jobs\BotAction;
use App\Providers\Helper\AIHandler;

class GameSessionObserver
{
    /**
     * @param GameSession $session
     */
    public function updated(GameSession $session)
    {
        if ($session->getOriginal('current_subscription_id') != $session->current_subscription_id) {
            $subscription = GameSubscription::find($session->current_subscription_id);
            foreach (AIHandler::BOT_LEVELS as $level => $bots) {
                if (in_array($subscription->user->name, $bots)) {
                    BotAction::dispatch($subscription)->delay(now()->addSeconds(1));
                    break;
                }
            }
        }
    }
}