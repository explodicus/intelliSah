<?php

namespace App\Entities;


use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Entities\GameSubscription
 *
 * @property-read \App\Entities\GameSession $session
 * @property-read \App\User $user
 * @mixin \Eloquent
 * @property int $id
 * @property int $session_id
 * @property int $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSubscription whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSubscription whereUserId($value)
 * @property int $side
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSubscription whereSide($value)
 */
class GameSubscription extends Model
{
    /**
     * @var string
     */
    protected $table = 'game_session_subscribers';

    /**
     * @var array
     */
    protected $guarded = [
        'created_at', 'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function session()
    {
        return $this->belongsTo(GameSession::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}