<?php

namespace App\Http\Controllers;

use App\Entities\GameSession;
use App\Entities\GameSubscription;
use App\Events\GameEvent;
use App\Events\SubscribeEvent;
use App\Providers\GameProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameSessionController extends Controller
{
    /**
     * GameSessionController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $sessions = GameSession::all();

        return view('sessions.index', [
            'sessions' => $sessions,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('sessions.new');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $session = new GameSession();
        $session->name = $request->name;
        $session->user_id = Auth::id();
        $session->save();

        return redirect()->route('sessions');
    }

    /**
     * @param GameSession $session
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(GameSession $session)
    {
        $session->subscribers;
        $session->subscriptions;

        $user = Auth::user();

        $currentSubscription = @$session->subscriptions->filter(function ($subscription) use ($user) {
            /** @var $subscription GameSubscription */
            if ($subscription->user_id === $user->id) {
                return true;
            }

            return false;
        })->pop();

        return view('sessions.show', [
            'sessionUser' => $session->user,
            'currentUser' => Auth::user(),
            'session' => $session,
            'currentSubscription' => $currentSubscription,
        ]);
    }

    /**
     * @param GameSession $session
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribe(GameSession $session)
    {
        $this->authorize('subscribe', $session);

        $otherSubscriptions = $session->subscriptions;

        $subscription = new GameSubscription();
        $subscription->user_id = Auth::id();
        $subscription->session_id = $session->id;

        $sides = range(0, 3);
        foreach ($otherSubscriptions as $otherSubscription) {
            unset($sides[$otherSubscription->side - 1]);
        }

        $subscription->side = array_pop($sides) + 1;
        $subscription->save();
        $otherSubscriptions->push($subscription);

        broadcast(new SubscribeEvent(Auth::user(), $session))->toOthers();

        if ($session->subscriptions->count() === 4) {
            $session->current_subscription_id = $session->subscriptions[0]->id;
            $gameProvider = new GameProvider();
            $session->game_bag = $gameProvider->initGameTable($session);
            $session->save();

            broadcast(new GameEvent($session));
        }

        $session->subscribers;
        return redirect()->route('session.show', [
            'session' => $session,
        ]);
    }

    /**
     * @param GameSession $session
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribers(GameSession $session)
    {
        return response()->json($session->subscribers);
    }
}
