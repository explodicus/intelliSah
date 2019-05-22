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

    public function testPerformance()
    {
        $handler = new AIHandler();

        $time = time();
        $session = GameSession::find(3);
        $handler->nextQuery($session);
        $perf1 = time() - $time;
        $this->assertTrue($perf1 <= 1);

        $time = time();
        $session = GameSession::find(5);
        $handler->nextQuery($session);
        $perf2 = time() - $time;
        $this->assertTrue($perf2 <= 2);

        $time = time();
        $session = GameSession::find(6);
        $handler->nextQuery($session);
        $perf3 = time() - $time;
        $this->assertTrue($perf3 <= 5);
    }
}
