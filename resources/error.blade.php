@php
    /**
     * @var \Throwable $exception
     * @var int $statusCode
     * @var array $frames
     */
    $activeFrame = $frames[0];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ class_basename($exception) }} - Hyde Exception Handler</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #1e1e1e;
            --bg-panel: #18181b;
            --bg-elevated: #252529;
            --bg-hover: #2a2a30;
            --border: #313136;
            --text: #d4d4d4;
            --text-muted: #9a9aa2;
            --text-dim: #6e6e78;
            --red: #f14c4c;
            --red-bg: rgba(241, 76, 76, 0.12);
            --purple: #c586c0;
            --teal: #4ec9b0;
            --blue: #9cdcfe;
            --yellow: #dcdcaa;
            --green: #6a9955;
            --font-ui: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            --font-mono: ui-monospace, SFMono-Regular, Menlo, Consolas, 'Liberation Mono', monospace;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-ui);
            min-height: 100vh;
        }

        header.topbar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: var(--bg-panel);
            border-bottom: 1px solid var(--border);
        }

        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 7px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            color: var(--purple);
            font-weight: 700;
            font-size: 13px;
        }

        .brand {
            font-weight: 600;
            font-size: 14px;
            color: var(--text);
        }

        .copy-report {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--bg-elevated);
            color: var(--text-muted);
            font-family: var(--font-ui);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .copy-report:hover {
            background: var(--bg-hover);
            color: var(--text);
        }

        .copy-report svg {
            width: 14px;
            height: 14px;
        }

        .copy-report.copied {
            color: var(--teal);
            border-color: var(--teal);
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(280px, 340px) 1fr;
            align-items: start;
        }

        /* Stack trace sidebar */
        aside.stack-trace {
            background: var(--bg-panel);
            border-right: 1px solid var(--border);
            position: sticky;
            top: 0;
            max-height: 100vh;
            overflow-y: auto;
        }

        .panel-heading {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            padding: 14px 16px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
        }

        .panel-heading .count {
            font-weight: 400;
            text-transform: none;
            letter-spacing: normal;
        }

        ol.frame-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        li.frame {
            display: flex;
            gap: 10px;
            padding: 10px 16px;
            border-bottom: 1px solid var(--border);
            border-left: 3px solid transparent;
            cursor: pointer;
        }

        li.frame:hover {
            background: var(--bg-hover);
        }

        li.frame.active {
            background: var(--red-bg);
            border-left-color: var(--red);
        }

        .frame-number {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            border-radius: 5px;
            background: var(--bg-elevated);
            color: var(--text-muted);
            font-size: 11px;
            font-family: var(--font-mono);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        li.frame.active .frame-number {
            background: var(--red);
            color: #fff;
        }

        .frame-body {
            min-width: 0;
        }

        .frame-file {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .frame-function {
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--purple);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .frame-path {
            font-size: 11px;
            color: var(--text-dim);
            word-break: break-all;
        }

        /* Main content */
        main.content {
            min-width: 0;
        }

        section.exception-banner {
            padding: 22px 24px;
            border-bottom: 1px solid var(--border);
        }

        .exception-heading {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .exception-icon {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--red-bg);
            color: var(--red);
            font-family: var(--font-mono);
            font-weight: 700;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .exception-heading h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
        }

        .badge {
            font-family: var(--font-mono);
            font-size: 12px;
            font-weight: 700;
            color: var(--red);
            background: var(--red-bg);
            border: 1px solid rgba(241, 76, 76, 0.35);
            border-radius: 5px;
            padding: 3px 8px;
        }

        .exception-location {
            margin-left: auto;
            text-align: right;
            font-family: var(--font-mono);
            font-size: 12px;
            color: var(--text-dim);
            word-break: break-all;
        }

        .exception-location .line {
            display: block;
            color: var(--text-muted);
        }

        p.exception-message {
            margin: 14px 0 0;
            font-size: 15px;
            color: var(--text-muted);
        }

        /* Code preview */
        section.code-preview {
            padding: 20px 24px;
        }

        .code-panel.hidden {
            display: none;
        }

        .code-frame {
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }

        .code-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            background: var(--bg-elevated);
            border-bottom: 1px solid var(--border);
            font-family: var(--font-mono);
            font-size: 12px;
        }

        .lang-badge {
            background: var(--bg-panel);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 1px 6px;
            color: var(--teal);
            font-weight: 700;
            font-size: 11px;
        }

        .code-filename {
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .code-line {
            margin-left: auto;
            color: var(--text-dim);
            flex-shrink: 0;
        }

        .code-body {
            overflow-x: auto;
        }

        table.code-table {
            border-collapse: collapse;
            width: 100%;
            font-family: var(--font-mono);
            font-size: 13px;
            line-height: 1.6;
        }

        table.code-table td {
            padding: 0 14px;
            white-space: pre;
        }

        table.code-table td.line-no {
            width: 1%;
            text-align: right;
            color: var(--text-dim);
            user-select: none;
            border-right: 1px solid var(--border);
        }

        table.code-table tr.highlight td.line-no {
            background: var(--red-bg);
            color: var(--red);
            font-weight: 700;
            border-right: 2px solid var(--red);
        }

        table.code-table tr.highlight td.line-code {
            background: var(--red-bg);
        }

        table.code-table td.line-code pre {
            margin: 0;
            display: inline;
        }

        .no-source {
            padding: 24px;
            color: var(--text-dim);
            font-size: 13px;
        }

        .tok-comment { color: var(--green); font-style: italic; }
        .tok-string { color: #ce9178; }
        .tok-variable { color: var(--blue); }
        .tok-number { color: #b5cea1; }
        .tok-keyword { color: var(--purple); }
        .tok-type { color: var(--teal); }

        /* Environment & request panel */
        section.environment-panel {
            padding: 20px 24px;
            border-top: 1px solid var(--border);
        }

        .env-card {
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--bg-panel);
            padding: 20px;
        }

        .env-heading {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .env-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .env-stat {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 160px;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            background: var(--bg-elevated);
        }

        .env-icon {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .env-icon svg {
            width: 18px;
            height: 18px;
        }

        .env-icon.icon-php {
            background: rgba(168, 85, 247, 0.15);
            color: #c084fc;
        }

        .env-icon.icon-hyde {
            background: rgba(197, 134, 192, 0.15);
            color: var(--purple);
            font-weight: 700;
            font-size: 13px;
        }

        .env-icon.icon-os {
            background: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
        }

        .env-stat-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }

        .env-stat-value {
            font-size: 12px;
            color: var(--text-muted);
        }

        .env-grid {
            display: grid;
            grid-template-columns: auto 1fr auto 1fr;
            column-gap: 20px;
            row-gap: 12px;
            align-items: baseline;
            font-size: 13px;
        }

        .env-key {
            color: var(--text-muted);
            white-space: nowrap;
        }

        .env-value {
            color: var(--text);
            word-break: break-all;
        }

        .env-value.mono {
            font-family: var(--font-mono);
            font-size: 12px;
        }

        .env-key.full {
            grid-column: 1;
        }

        .env-value.full {
            grid-column: 2 / -1;
        }
    </style>
</head>
<body>
<header class="topbar">
    <span class="logo">H</span>
    <span class="brand">Hyde Exception Handler</span>
    <button type="button" class="copy-report" id="copyReportButton">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="8" y="2" width="8" height="4" rx="1"></rect>
            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
        </svg>
        <span id="copyReportButtonLabel">Copy Report</span>
    </button>
</header>
<script id="reportData" type="application/json">{!! str_replace('</', '<\/', json_encode($report)) !!}</script>
<div class="layout">
    <aside class="stack-trace">
        <div class="panel-heading">
            <span>Stack trace</span>
            <span class="count">{{ count($frames) }} frames</span>
        </div>
        <ol class="frame-list">
            @foreach($frames as $frame)
                <li class="frame @if($loop->first) active @endif" data-frame="{{ $frame['number'] }}">
                    <span class="frame-number">{{ $frame['number'] }}</span>
                    <div class="frame-body">
                        <div class="frame-file">{{ $frame['file'] !== null ? basename($frame['file']) : '[internal function]' }}</div>
                        @if($frame['function'] !== null)
                            <div class="frame-function">{{ $frame['function'] }}</div>
                        @endif
                        @if($frame['relativeFile'] !== null)
                            <div class="frame-path">{{ $frame['relativeFile'] }}@if($frame['line'] !== null):{{ $frame['line'] }}@endif</div>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    </aside>
    <main class="content">
        <section class="exception-banner">
            <div class="exception-heading">
                <span class="exception-icon">!</span>
                <h1>{{ class_basename($exception) }}</h1>
                <span class="badge">{{ $statusCode }}</span>
                <div class="exception-location">
                    {{ $activeFrame['relativeFile'] ?? $activeFrame['file'] }}
                    @if($activeFrame['line'] !== null)
                        <span class="line">Line {{ $activeFrame['line'] }}</span>
                    @endif
                </div>
            </div>
            <p class="exception-message">{{ $exception->getMessage() }}</p>
        </section>
        <section class="code-preview">
            @foreach($frames as $frame)
                <div class="code-panel @if(! $loop->first) hidden @endif" data-frame="{{ $frame['number'] }}">
                    @if($frame['snippet'] !== null)
                        <div class="code-frame">
                            <div class="code-header">
                                <span class="lang-badge">PHP</span>
                                <span class="code-filename">{{ $frame['relativeFile'] }}</span>
                                <span class="code-line">Line {{ $frame['line'] }}</span>
                            </div>
                            <div class="code-body">
                                <table class="code-table">
                                    @foreach($frame['snippet']['lines'] as $number => $html)
                                        <tr class="@if($number === $frame['snippet']['highlightLine']) highlight @endif">
                                            <td class="line-no">{{ $number }}</td>
                                            <td class="line-code"><pre>{!! $html !!}</pre></td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="code-frame">
                            <div class="no-source">No source available for this frame.</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </section>
    </main>
</div>
<section class="environment-panel">
    <div class="env-card">
        <div class="env-heading">Environment &amp; Request</div>
        <div class="env-stats">
            <div class="env-stat">
                <span class="env-icon icon-php">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="16 18 22 12 16 6"></polyline>
                        <polyline points="8 6 2 12 8 18"></polyline>
                    </svg>
                </span>
                <div>
                    <div class="env-stat-label">PHP</div>
                    <div class="env-stat-value">{{ $environment['phpVersion'] }}</div>
                </div>
            </div>
            <div class="env-stat">
                <span class="env-icon icon-hyde">H</span>
                <div>
                    <div class="env-stat-label">Hyde</div>
                    <div class="env-stat-value">{{ $environment['hydeVersion'] }}</div>
                </div>
            </div>
            <div class="env-stat">
                <span class="env-icon icon-os">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="13" rx="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                </span>
                <div>
                    <div class="env-stat-label">OS</div>
                    <div class="env-stat-value">{{ $environment['os'] }}</div>
                </div>
            </div>
        </div>
        <div class="env-grid">
            <span class="env-key">GET Data</span>
            <span class="env-value">{{ $environment['getData'] }}</span>
            <span class="env-key">POST Data</span>
            <span class="env-value">{{ $environment['postData'] }}</span>

            <span class="env-key">Files</span>
            <span class="env-value">{{ $environment['files'] }}</span>
            <span class="env-key">Cookies</span>
            <span class="env-value">{{ $environment['cookies'] }}</span>

            <span class="env-key full">Session ID</span>
            <span class="env-value full mono">{{ $environment['sessionId'] ?? 'None' }}</span>

            <span class="env-key full">Time</span>
            <span class="env-value full">{{ $environment['time'] }}</span>
        </div>
    </div>
</section>
<script>
    document.querySelectorAll('.frame').forEach(function (frame) {
        frame.addEventListener('click', function () {
            var index = frame.getAttribute('data-frame');

            document.querySelectorAll('.frame').forEach(function (item) {
                item.classList.toggle('active', item === frame);
            });

            document.querySelectorAll('.code-panel').forEach(function (panel) {
                panel.classList.toggle('hidden', panel.getAttribute('data-frame') !== index);
            });
        });
    });

    (function () {
        var button = document.getElementById('copyReportButton');
        var label = document.getElementById('copyReportButtonLabel');
        var report = JSON.parse(document.getElementById('reportData').textContent);
        var defaultLabel = label.textContent;

        button.addEventListener('click', async function () {
            try {
                await navigator.clipboard.writeText(report);
            } catch (error) {
                window.prompt('Copy to clipboard: Ctrl+C, Enter', report);
            }

            button.classList.add('copied');
            label.textContent = 'Copied!';

            setTimeout(function () {
                button.classList.remove('copied');
                label.textContent = defaultLabel;
            }, 2000);
        });
    })();
</script>
</body>
</html>
