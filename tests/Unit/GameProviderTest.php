<?php

namespace Tests\Unit;

use App\Entities\GameSession;
use App\Providers\GameProvider;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GameProviderTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $session = GameSession::find(3);
        $provider = new GameProvider();

        $result = $provider->initGameTable($session);
        $this->assertArrayHasKey(1, $result);
    }
}
