@props([
    'title' => 'LiteraHub',
])

<x-layouts.app :title="$title">

    <header class="site-header">

        <div class="container nav">

            <a
                href="{{ route('home') }}"
                class="brand"
            >
                LiteraHub
            </a>

            <nav>

                <a href="{{ route('pricing') }}">
                    Pricing
                </a>

                @if(Route::has('login'))

                    <a
                        href="{{ route('login') }}"
                        class="button button-secondary button-small"
                    >
                        Sign In
                    </a>

                @endif

            </nav>

        </div>

    </header>

    <main class="section page-shell">

        <div
            class="container"
            style="max-width: 900px;"
        >

            @if(session('success'))

                <div
                    class="alert alert-success"
                    data-auto-dismiss="5000"
                >
                    {{ session('success') }}
                </div>

            @endif

            @if(session('error'))

                <div class="alert alert-error">
                    {{ session('error') }}
                </div>

            @endif

            {{ $slot }}

        </div>

    </main>

</x-layouts.app>