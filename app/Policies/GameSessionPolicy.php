<?php

namespace App\Policies;

use App\User;
use App\Entities\GameSession;
use Illuminate\Auth\Access\HandlesAuthorization;

class GameSessionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the game session.
     *
     * @param  \App\User $user
     * @param  \App\Entities\GameSession $gameSession
     * @return mixed
     */
    public function view(User $user, GameSession $gameSession)
    {
        //
    }

    /**
     * Determine whether the user can create game sessions.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the game session.
     *
     * @param  \App\User $user
     * @param  \App\Entities\GameSession $gameSession
     * @return mixed
     */
    public function update(User $user, GameSession $gameSession)
    {
        //
    }

    /**
     * Determine whether the user can delete the game session.
     *
     * @param  \App\User $user
     * @param  \App\Entities\GameSession $gameSession
     * @return mixed
     */
    public function delete(User $user, GameSession $gameSession)
    {
        //
    }

    /**
     * @param User $user
     * @param GameSession $gameSession
     * @return bool
     */
    public function subscribe(User $user, GameSession $gameSession)
    {
        return $gameSession->subscribers->count() < 4
            && $gameSession->subscribers->search(function ($sessionUser) use ($user) {
                return $sessionUser->id === $user->id;
            }) === false;
    }
}
