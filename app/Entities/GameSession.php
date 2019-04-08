<?php

namespace App\Entities;


use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Entities\GameSession
 *
 * @property int $id
 * @property string $name
 * @property int $status
 * @property int $user_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $current_user_id
 * @property array $game_bag
 * @property int $current_subscription_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $subscribers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\GameSubscription[] $subscriptions
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSession whereCurrentSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSession whereCurrentUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSession whereGameBag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSession whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSession whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSession whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Entities\GameSubscription $currentSubscription
 * @property string|null $fail_subscriptions
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSession whereFailSubscriptions($value)
 * @property int|null $gameover
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\GameSession whereGameover($value)
 */
class GameSession extends Model
{
    protected $casts = [
        'game_bag' => 'array',
        'fail_subscriptions' => 'array',
    ];

    protected $guarded = [
        'created_at', 'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function subscribers()
    {
        return $this->hasManyThrough(
            User::class,
            GameSubscription::class,
            'session_id',
            'id',
            'id',
            'user_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(GameSubscription::class, 'session_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentSubscription()
    {
        return $this->hasOne(GameSubscription::class);
    }
}