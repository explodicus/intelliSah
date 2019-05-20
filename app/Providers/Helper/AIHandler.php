<?php


namespace App\Providers\Helper;


use App\Entities\GameSession;

class AIHandler
{
    const BOT_LEVELS = [
        1 => [
            'BOT Aurel',
            'BOT Calin',
            'BOT Florica',
        ],
        2 => [
            'BOT Galina',
            'BOT Mugur',
            'BOT Norocel',
        ],
        3 => [
            'BOT Petru',
            'BOT Vadim',
            'BOT Gabi',
        ]
    ];

    public function canonical(GameSession $gameSession)
    {
        $canonical = array_reduce(range(0, 11), function ($carry, $row) {
            $carry[$row] = [];

            return $carry;
        }, []);

        foreach ($gameSession->game_bag as $row => $rowData) {
            foreach ($rowData as $column => $pieceBag) {
                list ($y, $x) = $this->flipToTopSide([$row, $column], $gameSession->currentSubscription->side);
                $pieceBag = json_decode($pieceBag, JSON_OBJECT_AS_ARRAY);
                $canonical[$y][$x] = [
                    'code' => $pieceBag['code'],
                    'ai' => (int)$pieceBag['subscription_id'] === $gameSession->current_subscription_id,
                    'row' => $y,
                    'column' => $x,
                ];
            }
        }

        return $canonical;
    }

    /**
     * @param array $position
     * @param $side
     * @return array
     * @throws \Exception
     */
    protected function flipToTopSide(array $position, $side)
    {
        list ($y, $x) = $position; // (2, 1) -> (10, 9)

        switch ($side) {
            case 3:
                $x = 11 - $x;
                $y = 11 - $y;
                break;
            case 4:
                $x1 = $x;
                $x = 11 - $y;
                $y = $x1;
                break;
            case 1:
                break;
            case 2:
                $x1 = $x;
                $x = 11 - $y;
                $y = 11 - $x1;
                break;
            default:
                throw new \Exception("Unexpected side");
        }
        return [$y, $x];
    }
}