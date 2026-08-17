<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Institutional Library
                </span>

                <h1>
                    Digital Library
                </h1>

                <p>
                    Browse books licensed to your institution
                    and available under your current access rights.
                </p>

            </div>


            @role('school_admin')

                <a
                    href="{{ route(
                        'school.library.licenses.catalogue'
                    ) }}"
                    class="btn btn--primary"
                >
                    Licence Catalogue
                </a>

            @endrole

        </div>


        {{-- ================================================================
             FILTERS
        ================================================================= --}}

        <div class="card">

            <form
                method="GET"
                action="{{ route(
                    'school.library.index'
                ) }}"
                class="library-filters"
            >

                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search title, author or ISBN..."
                >


                <select name="category">

                    <option value="">
                        All categories
                    </option>

                    @foreach ($categories as $category)

                        <option
                            value="{{ $category }}"
                            @selected(
                                request('category')
                                ===
                                $category
                            )
                        >
                            {{ $category }}
                        </option>

                    @endforeach

                </select>


                <button
                    type="submit"
                    class="btn btn--secondary"
                >
                    Filter
                </button>


                @if (
                    request('search')
                    ||
                    request('category')
                )

                    <a
                        href="{{ route(
                            'school.library.index'
                        ) }}"
                        class="btn btn--ghost"
                    >
                        Clear
                    </a>

                @endif

            </form>

        </div>


        {{-- ================================================================
             BOOKS
        ================================================================= --}}

        @if ($books->count())

            <div class="library-grid">

                @foreach ($books as $book)

                    <article class="library-book-card">

                        <a
                            href="{{ route(
                                'school.library.show',
                                $book
                            ) }}"
                            class="library-book-cover"
                        >

                            @if ($book->cover_path)

                                <img
                                    src="{{ asset(
                                        'storage/'
                                        . $book->cover_path
                                    ) }}"
                                    alt="{{ $book->title }}"
                                >

                            @else

                                <div class="library-cover-placeholder">

                                    <svg viewBox="0 0 24 24">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                                    </svg>

                                    <span>
                                        {{ $book->title }}
                                    </span>

                                </div>

                            @endif

                        </a>


                        <div class="library-book-body">

                            <div class="library-book-meta">

                                @if ($book->category)

                                    <span>
                                        {{ $book->category }}
                                    </span>

                                @endif

                                <x-library.book-status
                                    :status="$book->status"
                                />

                            </div>


                            <h2>

                                <a
                                    href="{{ route(
                                        'school.library.show',
                                        $book
                                    ) }}"
                                >
                                    {{ $book->title }}
                                </a>

                            </h2>


                            <p class="library-authors">

                                {{ $book->authors
                                    ->pluck('name')
                                    ->join(', ')
                                    ?: 'Unknown author'
                                }}

                            </p>


                            <p class="library-description">

                                {{ \Illuminate\Support\Str::limit(
                                    $book->description
                                    ?: 'No description available.',
                                    110
                                ) }}

                            </p>


                            <div class="library-book-footer">

                                <span>
                                    {{ $book->language }}
                                </span>


                                <a
                                    href="{{ route(
                                        'school.library.show',
                                        $book
                                    ) }}"
                                    class="library-open-link"
                                >
                                    View Book
                                </a>

                            </div>

                        </div>

                    </article>

                @endforeach

            </div>


            <div class="pagination-shell">
                {{ $books->links() }}
            </div>

        @else

            <div class="card empty-state">

                <h2>
                    No books available
                </h2>

                <p>
                    Your current catalogue filters or institutional
                    licence entitlements returned no available titles.
                </p>

            </div>

        @endif

    </div>


    <style>

        .library-filters {
            display: grid;
            grid-template-columns:
                minmax(260px, 1fr)
                190px
                auto
                auto;
            gap: 8px;
        }

        .library-filters input,
        .library-filters select {
            width: 100%;
        }

        .library-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(190px, 1fr)
                );
            gap: 14px;
        }

        .library-book-card {
            overflow: hidden;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            background: var(--color-surface);
        }

        .library-book-cover {
            display: block;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            background: var(--color-surface-soft);
        }

        .library-book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .library-cover-placeholder {
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 18px;
            color: var(--color-text-muted);
            text-align: center;
        }

        .library-cover-placeholder svg {
            width: 31px;
            height: 31px;
            fill: none;
            stroke: var(--color-primary);
            stroke-width: 1.5;
        }

        .library-cover-placeholder span {
            font-size: .58rem;
        }

        .library-book-body {
            padding: 13px;
        }

        .library-book-meta {
            min-height: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 5px;
        }

        .library-book-meta > span:first-child {
            color: var(--color-primary);
            font-size: .49rem;
            font-weight: 750;
            text-transform: uppercase;
        }

        .library-book-body h2 {
            margin: 7px 0 2px;
            font-size: .74rem;
            line-height: 1.35;
        }

        .library-book-body h2 a {
            color: var(--color-text);
            text-decoration: none;
        }

        .library-authors {
            margin: 0;
            color: var(--color-text-muted);
            font-size: .54rem;
        }

        .library-description {
            min-height: 43px;
            margin: 9px 0;
            color: var(--color-text-muted);
            font-size: .55rem;
            line-height: 1.55;
        }

        .library-book-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 7px;
            padding-top: 9px;
            border-top: 1px solid var(--color-border);
        }

        .library-book-footer span {
            color: var(--color-text-muted);
            font-size: .49rem;
        }

        .library-open-link {
            color: var(--color-primary);
            font-size: .53rem;
            font-weight: 750;
            text-decoration: none;
        }

        .pagination-shell {
            margin-top: 18px;
        }

        @media (max-width: 700px) {

            .library-filters {
                grid-template-columns: 1fr 1fr;
            }

        }

        @media (max-width: 480px) {

            .library-filters {
                grid-template-columns: 1fr;
            }

        }

    </style>

</x-layouts.dashboard>