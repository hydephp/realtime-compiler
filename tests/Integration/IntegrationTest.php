<?php

namespace Hyde\RealtimeCompiler\Tests\Integration;

class IntegrationTest extends IntegrationTestCase
{
    public function testRealtimeCompiler()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSeeText("You're running on HydePHP");
    }
}
