<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="theme-color"
        content="#312e81"
    >

    <title>
        {{ $title ?? config('app.name', 'LiteraHub') }}
    </title>
    
    <script>
    (() => {
        const saved =
            localStorage.getItem('literahub-theme');

        const theme =
            saved ||
            (
                window.matchMedia(
                    '(prefers-color-scheme: dark)'
                ).matches
                    ? 'dark'
                    : 'light'
            );

        document.documentElement.dataset.theme =
            theme;
    })();
    </script>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @livewireStyles

    @stack('styles')

</head>

<body>

    {{ $slot }}

    @livewireScripts

    @stack('scripts')

</body>

</html>
