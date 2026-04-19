<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Dominus') }} — Acesso</title>
    <link rel="icon" href="{{ asset('media/app/favicon.svg') }}" type="image/svg+xml" />

    @php
        $hotFilePath = public_path('react-app/hot');
        $devUrl = file_exists($hotFilePath) ? rtrim(trim((string) file_get_contents($hotFilePath)), '/') : '';
        $reactDev = $devUrl !== '';
        $manifestPath = public_path('react-app/.vite/manifest.json');
        $manifest = !$reactDev && file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : [];
        $entry = $manifest['index.html'] ?? ($manifest ? $manifest[array_key_first($manifest)] : null) ?? null;
    @endphp

    @if($reactDev)
        {{-- Em dev: preamble do @vitejs/plugin-react carregado do servidor Vite --}}
        <script type="module" src="{{ $devUrl }}/@@vite/client"></script>
        <script type="module">
            import RefreshRuntime from '{{ $devUrl }}/@@react-refresh'
            RefreshRuntime.injectIntoGlobalHook(window)
            window.$RefreshReg$ = () => {}
            window.$RefreshSig$ = () => (type) => type
            window.__vite_plugin_react_preamble_installed__ = true
        </script>
    @elseif($entry && !empty($entry['css']))
        @foreach($entry['css'] as $cssFile)
            <link rel="stylesheet" href="{{ global_asset('react-app/' . $cssFile) }}" />
        @endforeach
    @endif

    <script>
        window.__AUTH_APP_DATA__ = @json($authAppData);
    </script>
</head>
<body>
    <div id="root"></div>

    @if($reactDev)
        <script type="module" src="{{ $devUrl }}/src/main.tsx"></script>
    @elseif($entry)
        <script type="module" src="{{ global_asset('react-app/' . $entry['file']) }}"></script>
        @if(!empty($entry['imports']))
            @foreach($entry['imports'] as $importKey)
                @php $importEntry = $manifest[$importKey] ?? null; @endphp
                @if($importEntry)
                    <link rel="modulepreload" href="{{ global_asset('react-app/' . $importEntry['file']) }}" />
                @endif
            @endforeach
        @endif
    @else
        <div style="padding:2rem;font-family:sans-serif;color:#dc2626">
            <h2>React app não compilado</h2>
            <p>Execute <code>npm run build:react</code> para gerar os arquivos de produção.</p>
        </div>
    @endif
</body>
</html>
