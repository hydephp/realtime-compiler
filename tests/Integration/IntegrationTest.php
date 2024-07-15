<?php

namespace Hyde\RealtimeCompiler\Tests\Integration;

use ZipArchive;
use RuntimeException;
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

        $archive = 'https://github.com/hydephp/hyde/archive/refs/heads/master.zip';
        $target = __DIR__.'/../runner';

        $raw = file_get_contents($archive);

        if ($raw === false) {
            throw new RuntimeException('Failed to download test runner.');
        }

        $zipPath = tempnam(sys_get_temp_dir(), 'hyde-master');

        if ($zipPath === false) {
            throw new RuntimeException('Failed to create temporary file.');
        }

        file_put_contents($zipPath, $raw);

        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException('Failed to open zip archive.');
        }

        $zip->extractTo($target);

        $zip->close();

        unlink($zipPath);

        $runner = realpath($target.'/hyde-master/runner');
    }
}
