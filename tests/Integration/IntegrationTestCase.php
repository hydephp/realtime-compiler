<?php

namespace Hyde\RealtimeCompiler\Tests\Integration;

use ZipArchive;
use RuntimeException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    /** @var resource */
    protected static $server;

    public static function setUpBeforeClass(): void
    {
        $monorepo = realpath(__DIR__.'/../../../../');

        if ($monorepo && realpath(getcwd()) === $monorepo && file_exists($monorepo.'/hyde')) {
            throw new InvalidArgumentException('This test suite is not intended to be run from the monorepo.');
        }

        if (! self::hasTestRunnerSetUp()) {
            self::setUpTestRunner();
        }

        // Check that post 8080 is available
        $process = @fsockopen('localhost', 8080, $errno, $errstr, 1);

        if ($process) {
            fclose($process);
            throw new RuntimeException('Port 8080 is already in use.');
        }

        // Start the server in a background process, keeping the task ID for later
        self::$server = proc_open('cd '.realpath(__DIR__.'/../runner').' && php hyde serve', [], $pipes);
    }

    public function __destruct()
    {
        // Ensure the server is stopped, regardless of any errors
        if (self::$server) {
            proc_terminate(self::$server);
        }
    }

    protected static function hasTestRunnerSetUp(): bool
    {
        return file_exists(__DIR__.'/../runner');
    }

    protected static function setUpTestRunner(): void
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

        // Get the name of the root directory in the zip file
        $rootDir = $zip->getNameIndex(0);

        // Extract to a temporary directory
        $tempExtractPath = $target.'_temp';
        $zip->extractTo($tempExtractPath);

        $zip->close();

        // Move the contents of the extracted directory to the target directory
        rename($tempExtractPath.'/'.$rootDir, $target);

        // Remove the temporary extraction directory
        rmdir($tempExtractPath);

        unlink($zipPath);

        $runner = realpath($target);

        // Junction the package source of hyde/realtime-compiler to the test runner
        $branch = trim(shell_exec('git rev-parse --abbrev-ref HEAD') ?: 'master');
        shell_exec("cd $runner && composer config repositories.realtime-compiler path ../../");
        shell_exec("cd $runner && composer require --dev hyde/realtime-compiler:dev-$branch");
    }
}
