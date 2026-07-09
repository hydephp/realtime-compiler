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
    <meta name="csrf-token" content="{{ $csrfToken }}">
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

        .topbar-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ai-button-group {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 4px 6px 4px 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--bg-elevated);
        }

        .ai-group-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-right: 4px;
        }

        .ai-provider-btn {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border: none;
            border-radius: 5px;
            background: transparent;
            cursor: pointer;
        }

        .ai-provider-btn:hover {
            background: var(--bg-hover);
        }

        .ai-provider-btn svg {
            width: 14px;
            height: 14px;
        }

        .ai-provider-btn .ai-tooltip {
            position: absolute;
            top: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%) translateY(-4px);
            padding: 4px 8px;
            border-radius: 5px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            color: var(--text);
            font-family: var(--font-ui);
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.1s ease, transform 0.1s ease;
            z-index: 10;
        }

        .ai-provider-btn .ai-tooltip::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-bottom-color: var(--border);
        }

        .ai-provider-btn .ai-tooltip::after {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(1px);
            border: 4px solid transparent;
            border-bottom-color: var(--bg-elevated);
        }

        .ai-provider-btn:hover .ai-tooltip,
        .ai-provider-btn:focus-visible .ai-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        .ai-provider-btn.icon-chatgpt {
            color: #10a37f;
        }

        .ai-provider-btn.icon-claude {
            color: #d97757;
        }

        .ai-provider-btn.icon-perplexity {
            color: #20b8cd;
        }

        .copy-report {
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

        .open-in-editor {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
            padding: 3px 9px;
            border-radius: 5px;
            border: 1px solid var(--border);
            background: var(--bg-panel);
            color: var(--text-muted);
            font-family: var(--font-ui);
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
        }

        .open-in-editor:hover {
            background: var(--bg-hover);
            color: var(--text);
        }

        .open-in-editor svg {
            width: 12px;
            height: 12px;
        }

        .open-in-editor.success {
            color: var(--teal);
            border-color: var(--teal);
        }

        .open-in-editor.failure {
            color: var(--red);
            border-color: var(--red);
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

        /* Info panels row (Ask AI, Environment & Request, and any future panels) */
        .info-panels {
            padding: 20px 24px;
            border-top: 1px solid var(--border);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 16px;
            align-items: start;
        }

        .env-card,
        .docs-card {
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--bg-panel);
            padding: 20px;
        }

        .panel-heading-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .panel-icon {
            width: 15px;
            height: 15px;
            color: var(--purple);
            flex-shrink: 0;
        }

        .env-heading {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .panel-heading-row .env-heading {
            margin-bottom: 0;
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

        /* Documentation panel */
        .docs-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .docs-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--bg-elevated);
            color: inherit;
            text-decoration: none;
        }

        .docs-link:hover {
            background: var(--bg-hover);
            border-color: var(--text-dim);
        }

        .docs-link-icon {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .docs-link-icon svg {
            width: 18px;
            height: 18px;
        }

        .icon-docs {
            background: rgba(129, 140, 248, 0.15);
            color: #818cf8;
        }

        .icon-compiler {
            background: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
        }

        .icon-troubleshooting {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }

        .icon-issue {
            background: rgba(248, 113, 113, 0.15);
            color: #f87171;
        }

        .docs-link-text {
            flex: 1;
            min-width: 0;
        }

        .docs-link-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }

        .docs-link-subtitle {
            font-size: 11px;
            color: var(--text-dim);
            margin-top: 2px;
        }

        .docs-link-arrow {
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            color: var(--text-dim);
        }
    </style>
</head>
<body>
<header class="topbar">
    <span class="logo">H</span>
    <span class="brand">Hyde Exception Handler</span>
    <div class="topbar-actions">
        <div class="ai-button-group">
            <span class="ai-group-label">Ask AI</span>
            <button type="button" class="ai-provider-btn icon-chatgpt ai-provider" data-provider="chatgpt" aria-label="Ask ChatGPT about this error">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                    <circle cx="12" cy="7" r="4"></circle>
                    <circle cx="8" cy="15" r="4"></circle>
                    <circle cx="16" cy="15" r="4"></circle>
                </svg>
                <span class="ai-tooltip">ChatGPT</span>
            </button>
            <button type="button" class="ai-provider-btn icon-claude ai-provider" data-provider="claude" aria-label="Ask Claude about this error">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="12" y1="2" x2="12" y2="22"></line>
                    <line x1="2" y1="12" x2="22" y2="12"></line>
                    <line x1="4.9" y1="4.9" x2="19.1" y2="19.1"></line>
                    <line x1="19.1" y1="4.9" x2="4.9" y2="19.1"></line>
                </svg>
                <span class="ai-tooltip">Claude</span>
            </button>
            <button type="button" class="ai-provider-btn icon-perplexity ai-provider" data-provider="perplexity" aria-label="Ask Perplexity about this error">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round">
                    <polygon points="12,2 22,12 12,22 2,12"></polygon>
                    <polygon points="12,7 17,12 12,17 7,12"></polygon>
                </svg>
                <span class="ai-tooltip">Perplexity</span>
            </button>
        </div>
        <button type="button" class="copy-report" id="copyReportButton">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="8" y="2" width="8" height="4" rx="1"></rect>
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
            </svg>
            <span id="copyReportButtonLabel">Copy Report</span>
        </button>
    </div>
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
                                @if($openInEditorEnabled)
                                    <button type="button" class="open-in-editor" data-file="{{ $frame['file'] }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 3h7v7"></path>
                                            <path d="M10 14 21 3"></path>
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        </svg>
                                        <span class="open-in-editor-label">Open in Editor</span>
                                    </button>
                                @endif
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
        <div class="info-panels">
            <section class="docs-card">
                <div class="panel-heading-row">
                    <svg class="panel-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <div class="env-heading">Documentation</div>
                </div>
                <div class="docs-links" style="margin-top: 16px;">
                    <a class="docs-link" href="https://hydephp.com/docs/2.x/" target="_blank" rel="noopener">
                        <span class="docs-link-icon icon-docs">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                        </span>
                        <span class="docs-link-text">
                            <div class="docs-link-title">Documentation</div>
                            <div class="docs-link-subtitle">Browse the full Hyde docs</div>
                        </span>
                        <svg class="docs-link-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <a class="docs-link" href="https://hydephp.com/docs/2.x/realtime-compiler" target="_blank" rel="noopener">
                        <span class="docs-link-icon icon-compiler">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                            </svg>
                        </span>
                        <span class="docs-link-text">
                            <div class="docs-link-title">Realtime Compiler</div>
                            <div class="docs-link-subtitle">How the compiler works</div>
                        </span>
                        <svg class="docs-link-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <a class="docs-link" href="https://hydephp.com/docs/2.x/troubleshooting" target="_blank" rel="noopener">
                        <span class="docs-link-icon icon-troubleshooting">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </span>
                        <span class="docs-link-text">
                            <div class="docs-link-title">Troubleshooting Guide</div>
                            <div class="docs-link-subtitle">Common issues and fixes</div>
                        </span>
                        <svg class="docs-link-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <a class="docs-link" href="https://github.com/hydephp/hyde/issues" target="_blank" rel="noopener">
                        <span class="docs-link-icon icon-issue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                        </span>
                        <span class="docs-link-text">
                            <div class="docs-link-title">Report an Issue</div>
                            <div class="docs-link-subtitle">Found a bug? Let us know</div>
                        </span>
                        <svg class="docs-link-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>
            </section>
            <section class="env-card">
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
            </section>
        </div>
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

    document.querySelectorAll('.open-in-editor').forEach(function (button) {
        var label = button.querySelector('.open-in-editor-label');
        var defaultLabel = label.textContent;
        var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function reset() {
            button.classList.remove('success', 'failure');
            label.textContent = defaultLabel;
        }

        button.addEventListener('click', async function () {
            try {
                var response = await fetch('/_hyde/open-in-editor', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: new URLSearchParams({
                        file: button.getAttribute('data-file'),
                        _token: csrfToken,
                    }),
                });

                if (response.ok) {
                    button.classList.add('success');
                    label.textContent = 'Opened!';
                } else {
                    var payload = await response.json().catch(function () {
                        return {};
                    });
                    button.classList.add('failure');
                    label.textContent = payload.error || 'Failed';
                }
            } catch (error) {
                button.classList.add('failure');
                label.textContent = 'Failed';
            }

            setTimeout(reset, 2500);
        });
    });

    (function () {
        var report = JSON.parse(document.getElementById('reportData').textContent);
        var maxPromptLength = 6000;
        var defaultInstruction = 'Explain this error and suggest a fix';

        function getActiveCodeText() {
            var panel = document.querySelector('.code-panel:not(.hidden)');

            if (!panel) {
                return null;
            }

            var lines = panel.querySelectorAll('.line-code pre');

            if (!lines.length) {
                return null;
            }

            return Array.prototype.map.call(lines, function (el) {
                return el.textContent;
            }).join('\n');
        }

        function buildPrompt() {
            var codeText = getActiveCodeText();
            var parts = [defaultInstruction, '', report];

            if (codeText) {
                parts.push('', 'Code:', '```php', codeText, '```');
            }

            var prompt = parts.join('\n');

            if (prompt.length > maxPromptLength) {
                prompt = prompt.slice(0, maxPromptLength) + '\n... (truncated)';
            }

            return prompt;
        }

        function providerUrl(provider, prompt) {
            var encoded = encodeURIComponent(prompt);

            switch (provider) {
                case 'chatgpt':
                    return 'https://chatgpt.com/?q=' + encoded;
                case 'claude':
                    return 'claude://claude.ai/new?q=' + encoded;
                case 'perplexity':
                    return 'https://www.perplexity.ai/search?q=' + encoded;
                default:
                    return null;
            }
        }

        function openProvider(provider) {
            var url = providerUrl(provider, buildPrompt());

            if (!url) {
                return;
            }

            if (provider === 'claude') {
                // Deep link into the Claude Desktop app rather than a browser tab.
                window.location.href = url;
            } else {
                window.open(url, '_blank', 'noopener');
            }
        }

        document.querySelectorAll('.ai-provider').forEach(function (button) {
            button.addEventListener('click', function () {
                openProvider(button.getAttribute('data-provider'));
            });
        });
    })();
</script>
</body>
</html>
