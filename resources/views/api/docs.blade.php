<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>API documentation | Riva Motors</title>
    <meta name="description" content="Riva Motors API: cars for sale, extras, finance quotes and reservations.">
    <link rel="icon" href="{{ img('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css'])
    <style>
        /* A slim bar above the documentation, in the site's style */
        .docs-bar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px 24px;
            padding: 12px 24px;
            background: #121418;
            color: #f4f5f7;
        }
        .docs-bar .brand { color: #f4f5f7; }
        .docs-bar .brand:hover { color: #ffffff; }
        .docs-bar__title { font-weight: 650; color: #a3a9b3; }
        .docs-bar nav { display: flex; flex-wrap: wrap; gap: 8px; margin-left: auto; }
        .docs-bar nav a {
            height: 36px;
            padding: 0 14px;
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            color: #f4f5f7;
            text-decoration: none;
            font-size: 13px;
            font-weight: 650;
        }
        .docs-bar nav a:hover { background: rgba(255, 255, 255, 0.08); color: #ffffff; }
        body { background: #ffffff; }
        #redoc-container { min-height: 60vh; }
        .docs-loading { padding: 48px 24px; color: #565c66; }
    </style>
</head>
<body>
    <header class="docs-bar">
        <a class="brand" href="{{ page('home') }}" aria-label="Riva Motors home">
            <x-logo :size="32" />
            <span class="brand__name">Riva Motors</span>
        </a>
        <span class="docs-bar__title">API documentation, v1</span>
        <nav aria-label="API links">
            <a href="{{ img('api/openapi.yaml') }}" download>Download OpenAPI file</a>
            <a href="{{ img('api/v1/index.json') }}">Try the API</a>
            <a href="{{ page('home') }}">Back to the site</a>
        </nav>
    </header>

    <main id="redoc-container">
        <p class="docs-loading">Loading the documentation&hellip;</p>
    </main>

    <noscript><p class="docs-loading">The documentation needs JavaScript. You can read the <a href="{{ img('api/openapi.yaml') }}">OpenAPI file</a> directly.</p></noscript>

    {{-- Redoc renders public/api/openapi.yaml, the single source of truth for this documentation --}}
    <script src="https://cdn.jsdelivr.net/npm/redoc@2.1.5/bundles/redoc.standalone.js"></script>
    <script>
        Redoc.init(@json(img('api/openapi.yaml')), {
            hideDownloadButton: false,
            expandResponses: '200,201',
            jsonSampleExpandLevel: 3,
            requiredPropsFirst: true,
            sortPropsAlphabetically: false,
            pathInMiddlePanel: true,
            theme: {
                colors: { primary: { main: '#c4470f' }, success: { main: '#1d7a3e' }, error: { main: '#b3261e' } },
                typography: {
                    fontFamily: '"Archivo Variable", Archivo, "Helvetica Neue", Arial, sans-serif',
                    headings: { fontFamily: '"Archivo Variable", Archivo, "Helvetica Neue", Arial, sans-serif', fontWeight: '750' },
                    code: { fontFamily: '"IBM Plex Mono", ui-monospace, Menlo, Consolas, monospace' },
                },
                sidebar: { backgroundColor: '#f1f2f4', activeTextColor: '#c4470f' },
                rightPanel: { backgroundColor: '#16181c' },
            },
        }, document.getElementById('redoc-container'), (error) => {
            if (error) {
                document.getElementById('redoc-container').innerHTML =
                    '<p class="docs-loading">The documentation could not be loaded. Read the <a href="' + @json(img('api/openapi.yaml')) + '">OpenAPI file</a> directly.</p>';
            }
        });
    </script>
</body>
</html>
