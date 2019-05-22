<?php

namespace Tests\Unit;

use App\Entities\GameSession;
use App\Models\Piece;
use App\Providers\Helper\PieceValidator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PieceValidatorTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        /** @var GameSession $session */
        $session = GameSession::find(3);
        $validator = new PieceValidator();
        $this->assertTrue(
            $validator->validatePieceToPosition(Piece::fromArray($session->game_bag[0][2]), 1, [1, 2])
        );
    }
}
