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
            height: 100%;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-ui);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header.topbar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: var(--bg-panel);
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
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

        .layout {
            flex: 1;
            display: grid;
            grid-template-columns: minmax(280px, 340px) 1fr;
            min-height: 0;
        }

        /* Stack trace sidebar */
        aside.stack-trace {
            background: var(--bg-panel);
            border-right: 1px solid var(--border);
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
            display: flex;
            flex-direction: column;
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
            flex: 1;
            padding: 20px 24px;
            overflow: auto;
            min-height: 0;
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
    </style>
</head>
<body>
<header class="topbar">
    <span class="logo">H</span>
    <span class="brand">Hyde Exception Handler</span>
</header>
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
</script>
</body>
</html>
