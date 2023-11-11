<?php

declare(strict_types=1);

namespace Hyde\RealtimeCompiler;

use Closure;
use Hyde\Hyde;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Symfony\Component\Console\Output\ConsoleOutput as SymfonyOutput;

use function Termwind\render;

class ConsoleOutput
{
    protected bool $verbose;
    protected SymfonyOutput $output;

    public function __construct(bool $verbose = false, ?SymfonyOutput $output = null)
    {
        $this->verbose = $verbose;
        $this->output = $output ?? new SymfonyOutput();
    }

    public function printStartMessage(string $host, int $port): void
    {
        $title = 'HydePHP Realtime Compiler';
        $version = ' v'.Hyde::version();

        $url = sprintf('%s://%s:%d', $port === 443 ? 'https' : 'http', $host, $port);

        $width = max(strlen("$title $version"), strlen("Listening on $url") + 1) + 1;
        $spacing = str_repeat('&nbsp;', $width);
        $lines = str_repeat('─', $width);

        $line1 = '&nbsp;'.sprintf('<span class="text-blue-500">%s</span>&nbsp;<span class="text-gray">%s</span>', $title, $version).str_repeat('&nbsp;', $width - strlen("$title $version"));
        $line2 = '&nbsp;'.sprintf('<span class="text-white">Listening on </span>&nbsp;<a href="%s" class="text-yellow-500">%s</a>', $url, $url).str_repeat('&nbsp;', $width - strlen("Listening on $url") - 1);
        render(<<<HTML
<div class="text-green-500">
<br>
&nbsp;╭{$lines}╮<br>
&nbsp;│{$spacing}│<br>
&nbsp;│{$line1}│<br>
&nbsp;│{$spacing}│<br>
&nbsp;│{$line2}│<br>
&nbsp;│{$spacing}│<br>
&nbsp;╰{$lines}╯
<br>
</div>
HTML);
    }

    public function getFormatter(): Closure
    {
        return function (string $type, string $line): void {
            $this->handleOutput($line);
        };
    }

    /** @experimental */
    public function printMessage(string $message, string $context): void
    {
        $this->output->writeln(sprintf('%s [%s]', $message, $context));
    }

    protected function handleOutput(string $buffer): void
    {
        str($buffer)->trim()->explode("\n")->each(function (string $line): void {
            $this->renderLine($this->formatLineForOutput($line));
        });
    }

    protected function renderLine(?string $line): void
    {
        if ($line !== null) {
            render($line);
        }
    }

    protected function formatLineForOutput(string $line): ?string
    {
        if (str_contains($line, 'Development Server (http:')) {
            return $this->formatServerStartedLine($line);
        }
        if (str_contains($line, ']: ')) {
            return $this->formatRequestLine($line);
        }
        if (str_ends_with(trim($line), 'Accepted') || str_ends_with(trim($line), 'Closing')) {
            return $this->verbose ? $this->formatRequestStatusLine($line) : null;
        }
        if (str_contains($line, '[dashboard@')) {
            return $this->formatDashboardContextLine($line);
        }

        return $this->formatLine($line, Carbon::now());
    }

    protected function formatServerStartedLine(string $line): string
    {
        return $this->formatLine(sprintf('PHP %s Development Server started. <span class="text-yellow-500">Press Ctrl+C to stop.</span>', PHP_VERSION), $this->parseDate($line), 'green-500');
    }

    protected function formatRequestLine(string $line): string
    {
        $dateString = Str::betweenFirst($line, '[', ']');
        $message = substr($line, strlen($dateString) + 3);

        $statusCode = Str::between($message, ' [', ']:');
        if ($statusCode >= 400) {
            $message = str_replace($statusCode, sprintf('<span class="text-red-500">%s</span>', $statusCode), $message);
            $iconColor = 'yellow-500';
        }

        return $this->formatLine($message, $this->parseDate($line), $iconColor ?? 'blue-500');
    }

    protected function formatRequestStatusLine(string $line): string
    {
        $address = trim(Str::between($line, ']', ' '));
        $status = str_contains($line, 'Accepted') ? 'Accepted' : 'Closing';

        return $this->formatLine(sprintf('%s %s', $address, $status), $this->parseDate($line));
    }

    protected function formatDashboardContextLine(string $line): string
    {
        $message = trim(Str::before($line, '[dashboard@'));
        $context = trim(trim(Str::after($line, $message)), '[]');
        $success = str_contains($message, 'Created') || str_contains($message, 'Updated');

        return $this->formatLine($message, Carbon::now(), $success ? 'green-500' : 'blue-500', $context);
    }

    protected function formatLine(string $message, Carbon $date, string $iconColor = 'blue-500', string $context = ''): string
    {
        if ($context) {
            $context = "$context ";
        }

        return sprintf(<<<'HTML'
            <div class="flex w-full justify-between">
                <span>
                    <span class="text-%s">i</span>
                    %s
                </span>
                <span class="text-gray">%s%s</span>
            </div>
            HTML,
            $iconColor, $message, $context, $date->format('Y-m-d H:i:s')
        );
    }

    protected function parseDate(string $line): Carbon
    {
        return Carbon::parse(Str::betweenFirst($line, '[', ']'));
    }
}
