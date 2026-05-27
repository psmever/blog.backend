<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'blog.api server' }}</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f6f7f9;
            --panel: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --line: #e5e7eb;
            --accent: #2563eb;
            --accent-soft: #eff6ff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 32px;
            background: var(--bg);
            color: var(--text);
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        main {
            width: min(100%, 920px);
            min-height: 560px;
            padding: 64px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--panel);
            box-shadow: 0 18px 45px rgb(15 23 42 / 8%);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 18px;
            padding: 6px 10px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 13px;
            font-weight: 700;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #16a34a;
        }

        h1 {
            margin: 0;
            font-size: clamp(44px, 8vw, 76px);
            line-height: 1;
            letter-spacing: 0;
        }

        p {
            max-width: 680px;
            margin: 24px 0 0;
            color: var(--muted);
            font-size: 19px;
            line-height: 1.7;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 32px;
        }

        .meta-item {
            min-width: 0;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 8px;
        }

        .meta-label {
            margin-bottom: 6px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .meta-value {
            overflow-wrap: anywhere;
            font-size: 15px;
            font-weight: 700;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 28px;
        }

        a {
            display: inline-flex;
            align-items: center;
            min-height: 44px;
            padding: 0 16px;
            border-radius: 8px;
            border: 1px solid var(--line);
            color: var(--text);
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
        }

        a.primary {
            border-color: var(--accent);
            background: var(--accent);
            color: #ffffff;
        }

        @media (max-width: 640px) {
            body {
                padding: 20px;
            }

            main {
                min-height: auto;
                padding: 36px 28px;
            }

            .meta {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main>
        <div class="eyebrow">
            <span class="status-dot" aria-hidden="true"></span>
            Backend Online
        </div>

        <h1>blog.api server</h1>
        <p>
            {{ config('app.name') }} 백엔드 API 서버입니다.
            프런트엔드 애플리케이션과 관리 도구는 이 서버의 API를 통해 데이터를 주고받습니다.
        </p>

        <div class="meta" aria-label="server information">
            <div class="meta-item">
                <div class="meta-label">Environment</div>
                <div class="meta-value">{{ app()->environment() }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Laravel</div>
                <div class="meta-value">{{ app()->version() }}</div>
            </div>
            <div class="meta-item">
                <div class="meta-label">Time</div>
                <div class="meta-value">{{ now()->toDateTimeString() }}</div>
            </div>
        </div>

        <div class="actions">
            <a class="primary" href="/api/health">Health Check</a>
            <a href="/api/v1/base-data">Base Data</a>
        </div>
    </main>
</body>
</html>
