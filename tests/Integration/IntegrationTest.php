<?php

namespace Hyde\RealtimeCompiler\Tests\Integration;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $monorepo = realpath(__DIR__.'/../../../../');

        if ($monorepo && realpath(getcwd()) === $monorepo && file_exists($monorepo.'/hyde')) {
            throw new InvalidArgumentException('This test suite is not intended to be run from the monorepo.');
        }

        if (! self::hasTestRunnerSetUp()) {
            self::setUpTestRunner();
        }
    }

    public function testExample()
    {
        $this->assertTrue(true);
    }

    private static function hasTestRunnerSetUp(): bool
    {
        return file_exists(__DIR__.'/../runner');
    }

    private static function setUpTestRunner(): void
    {
        echo "\33[33mSetting up test runner...\33[0m This may take a while.\n";

        shell_exec('cd '.__DIR__.'/../ && git clone https://github.com/hydephp/hyde.git runner');
    }
}
