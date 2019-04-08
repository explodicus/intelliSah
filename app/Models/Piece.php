<?php

namespace App\Models;


use App\Entities\GameSession;
use App\Entities\GameSubscription;
use App\User;

class Piece implements \JsonSerializable
{
    const KING = 'king';
    const QUEEN = 'queen';
    const ROOK = 'rook';
    const BISHOP = 'bishop';
    const KNIGHT = 'knight';
    const PAWN = 'pawn';

    /**
     * @var GameSubscription
     */
    public $subscription;

    /**
     * @var string
     */
    public $code;

    /**
     * @var array
     */
    public $position;

    /**
     * Piece constructor.
     * @param GameSession $subscription
     * @param $code
     * @param array $position
     */
    public function __construct(GameSubscription $subscription, $code, array $position)
    {
        $this->subscription = $subscription;
        $this->code = $code;
        $this->position = $position;
    }

    /**
     * @param array $bag
     * @return Piece
     * @throws \Exception
     */
    public static function fromArray(array $bag)
    {
        $session = GameSubscription::find($bag['subscription_id']);
        if (!$session) {
            throw new \Exception('Not found session');
        }

        return new Piece($session, $bag['code'], $bag['position']);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'subscription_id' => $this->subscription->id,
            'code' => $this->code,
            'position' => $this->position,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return json_encode($this->toArray());
    }
}