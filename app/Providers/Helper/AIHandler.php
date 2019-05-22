<?php


namespace App\Providers\Helper;


use App\Entities\GameSession;
use App\Entities\GameSubscription;
use Illuminate\Support\Facades\Log;

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

    /**
     * @param GameSession $session
     * @return array
     * @throws \Exception
     */
    public function nextQuery(GameSession $session)
    {
        $cmd = sprintf(
            "python %s '%s'",
            base_path('chess-ai-master/play.py'),
            json_encode($this->canonical($session))
        );

        Log::error($cmd);
        $response = shell_exec($cmd);
        list ($from, $to) = array_values(json_decode($response, JSON_OBJECT_AS_ARRAY));
        $side = $session->currentSubscription->side;

        return [$this->unFlipFromTopSide($from, $side), $this->unFlipFromTopSide($to, $side)];
    }

    /**
     * @param GameSession $gameSession
     * @return mixed
     * @throws \Exception
     */
    protected function canonical(GameSession $gameSession)
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
                    'position' => [$y, $x],
                ];
            }
        }

        return [
            'board' => $canonical,
            'level' => (int)$this->getBotLevel($gameSession->currentSubscription) + 1,
        ];
    }

    /**
     * @param GameSubscription $subscription
     * @return int
     */
    protected function getBotLevel(GameSubscription $subscription)
    {
        foreach (self::BOT_LEVELS as $level => $bots) {
            if (in_array($subscription->user->name, $bots)) {
                return $level;
            }
        }

        return 0;
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
                $y1 = $y;
                $y = $x;
                $x = 11 - $y1;
                break;
            case 1:
                break;
            case 2:
                $x1 = $x;
                $x = $y;
                $y = 11 - $x1;
                break;
            default:
                throw new \Exception("Unexpected side");
        }

        return [$y, $x];
    }

    /**
     * @param array $position
     * @param $side
     * @return array
     * @throws \Exception
     */
    protected function unFlipFromTopSide(array $position, $side)
    {
        list ($y, $x) = $position; // (2, 1) -> (10, 9)
        switch ($side) {
            case 3:
                $x = 11 - $x;
                $y = 11 - $y;
                break;
            case 2:
                $y1 = $y;
                $y = $x;
                $x = 11 - $y1;
                break;
            case 1:
                break;
            case 4:
                $x1 = $x;
                $x = $y;
                $y = 11 - $x1;
                break;
            default:
                throw new \Exception("Unexpected side");
        }

        return [$y, $x];
    }
}