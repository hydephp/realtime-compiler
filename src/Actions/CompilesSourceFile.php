<?php

namespace Hyde\RealtimeCompiler\Actions;

use Hyde\RealtimeCompiler\Server;

/**
 * Hook into Hyde to compile a source file.
 */
class CompilesSourceFile
{
    /**
     * The source file to compile.
     * @var string
     */
    private string $path;

    /**
     * Construct a new Action instance.
     */
    public function __construct(string $path)
    {
        // Remove everything before the first underscore
        $this->path = substr($path, strpos($path, '_'));
    }

    /**
     * Trigger the compiler and return the compiled HTML string.
     * @return string
     */
    public function execute(): bool|string
    {
        Server::log('Compiler: Building page...');
        $output =  shell_exec('php '.HYDE_PATH.'/hyde rebuild '.$this->path);
        Server::log('Compiler: ' . $output);

        return $this->catchExceptions($output) ?:
            file_get_contents(HYDE_PATH . '/_site/'. $this->formatPathname());
    }

    /**
     * Convert the source path string to the output path string.
     * @return string
     */
    private function formatPathname(): string
    {
        $filename = $this->path;

        $filename = str_replace('_pages', '', $filename);
        $filename = str_replace('_', '', $filename);
        $filename = str_replace('.blade.php', '.html', $filename);
        $filename = str_replace('.md', '.html', $filename);

        return $filename;
    }

    /**
     * (Try to) Catch any exceptions, otherwise return false if it's safe to proceed.
     */
    private function catchExceptions(string $output): string|false
    {
        // Might be too general, can always add an array of FQSN exceptions
        if (strpos($output, 'Exception') !== false) {
            Server::log("Error: \033[0;31mException detected in output.\033[0m\n");
            return "<h1>Error: Exception  detected in output.</h1>\n<pre>".e($output).'</pre>';
        }

        return false;
    }
}