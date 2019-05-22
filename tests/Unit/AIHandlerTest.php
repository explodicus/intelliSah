<?php

namespace Tests\Unit;

use App\Entities\GameSession;
use App\Providers\Helper\AIHandler;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIHandlerTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $session = GameSession::find(3);
        $handler = new AIHandler();
        $result = $handler->nextQuery($session);

        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
    }
}
