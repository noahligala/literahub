@props([
    'title' => 'Dashboard — LiteraHub',
])

<x-layouts.app :title="$title">

    <div class="app-shell">

        <header class="app-header">

            <div class="app-header__inner">

                <div class="app-header__start">

                    <button
                        type="button"
                        class="icon-button mobile-menu-button"
                        aria-label="Open navigation"
                        aria-controls="mobile-navigation"
                        aria-expanded="false"
                        data-nav-toggle
                    >
                        <svg
                            width="22"
                            height="22"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M4 6h16"></path>
                            <path d="M4 12h16"></path>
                            <path d="M4 18h16"></path>
                        </svg>
                    </button>

                    <a
                        href="{{ route('dashboard') }}"
                        class="brand"
                    >
                        LiteraHub
                    </a>

                </div>

                <div class="desktop-navigation">
                    <x-navigation.dashboard />
                </div>

                <div class="app-header__actions">

    <button
        type="button"
        class="icon-button"
        aria-label="Toggle theme"
        onclick="LiteraHub.toggleTheme()"
    >
        <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <circle cx="12" cy="12" r="4"></circle>
            <path d="M12 2v2"></path>
            <path d="M12 20v2"></path>
            <path d="M4.93 4.93l1.41 1.41"></path>
            <path d="M17.66 17.66l1.41 1.41"></path>
            <path d="M2 12h2"></path>
            <path d="M20 12h2"></path>
        </svg>
    </button>

    @auth

        <div class="user-summary">

            <span class="user-avatar">
                {{ strtoupper(
                    substr(
                        auth()->user()->name,
                        0,
                        1
                    )
                ) }}
            </span>

            <span class="user-summary__name">
                {{ auth()->user()->name }}
            </span>

        </div>

    @else

        <a
            href="{{ route('login') }}"
            class="button button-secondary button-small"
        >
            Login
        </a>

        <a
            href="{{ route('register') }}"
            class="button button-small"
        >
            Get Started
        </a>

    @endauth

</div>

            </div>

        </header>

        {{-- Mobile navigation drawer --}}
        <div
            class="mobile-nav-backdrop"
            data-nav-backdrop
        ></div>

        <aside
            id="mobile-navigation"
            class="mobile-nav"
            data-mobile-nav
        >

            <div class="mobile-nav__header">

                <a
                    href="{{ route('dashboard') }}"
                    class="brand"
                >
                    LiteraHub
                </a>

                <button
                    type="button"
                    class="icon-button"
                    aria-label="Close navigation"
                    data-nav-close
                >
                    ✕
                </button>

            </div>

            @auth

    <div class="mobile-nav__user">

        <span class="user-avatar user-avatar--large">
            {{ strtoupper(
                substr(
                    auth()->user()->name,
                    0,
                    1
                )
            ) }}
        </span>

        <div>

            <strong>
                {{ auth()->user()->name }}
            </strong>

            <small>
                {{ auth()->user()->email }}
            </small>

        </div>

    </div>

@endauth

            <div class="mobile-nav__links">

                <x-navigation.dashboard />

            </div>

            <div class="mobile-nav__footer">

                <button
                    type="button"
                    class="button button-secondary button-block"
                    onclick="LiteraHub.toggleTheme()"
                >
                    Change Theme
                </button>

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >
                    @csrf

                    <button
                        type="submit"
                        class="button button-ghost button-block"
                    >
                        Sign Out
                    </button>

                </form>

            </div>

        </aside>

        <main class="app-main">

            <div class="container">

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

    </div>

</x-layouts.app>