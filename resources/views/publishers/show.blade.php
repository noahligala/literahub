<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Publisher
                </span>

                <h1>
                    {{ $publisher->name }}
                </h1>

                <p>
                    {{ $publisher->registration_number
                        ?: 'Publishing partner'
                    }}
                </p>

            </div>


            <div class="page-header__actions">

                <a
                    href="{{ route('publishers.index') }}"
                    class="btn btn--secondary"
                >
                    Back
                </a>


                @can('update', $publisher)

                    <a
                        href="{{ route(
                            'publishers.edit',
                            $publisher
                        ) }}"
                        class="btn btn--primary"
                    >
                        Edit Publisher
                    </a>

                @endcan

            </div>

        </div>


        <div class="entity-details-layout">

            <div class="entity-details-main">

                {{-- Overview --}}
                <section class="card entity-details-card">

                    <div class="entity-details-header">

                        <div>
                            <span class="eyebrow">
                                Profile
                            </span>

                            <h2>
                                Publisher Overview
                            </h2>
                        </div>


                        @php
                            $publisherStatus = match ($publisher->status) {
                                'active' => 'badge--success',
                                'pending' => 'badge--warning',
                                'suspended' => 'badge--danger',
                                default => 'badge--muted',
                            };
                        @endphp

                        <span class="badge {{ $publisherStatus }}">
                            {{ str($publisher->status)->title() }}
                        </span>

                    </div>


                    <div class="publisher-profile">

                        <div class="publisher-logo">

                            @if ($publisher->logo_path)

                                <img
                                    src="{{ asset(
                                        'storage/'
                                        . $publisher->logo_path
                                    ) }}"
                                    alt="{{ $publisher->name }}"
                                >

                            @else

                                <span>
                                    {{ collect(
                                        preg_split(
                                            '/\s+/',
                                            trim($publisher->name)
                                        )
                                    )
                                        ->filter()
                                        ->take(2)
                                        ->map(
                                            fn ($part) =>
                                                strtoupper(
                                                    mb_substr($part, 0, 1)
                                                )
                                        )
                                        ->implode('')
                                    }}
                                </span>

                            @endif

                        </div>


                        <div class="publisher-profile__body">

                            <h3>
                                {{ $publisher->name }}
                            </h3>

                            <p>
                                {{ $publisher->description
                                    ?: 'No publisher description has been provided.'
                                }}
                            </p>


                            <div class="entity-meta-grid">

                                <div>
                                    <span>Registration</span>
                                    <strong>
                                        {{ $publisher->registration_number ?? '—' }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Email</span>
                                    <strong>
                                        {{ $publisher->email ?? '—' }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Phone</span>
                                    <strong>
                                        {{ $publisher->phone ?? '—' }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Website</span>

                                    @if ($publisher->website)

                                        <strong>
                                            <a
                                                href="{{ $publisher->website }}"
                                                target="_blank"
                                                rel="noopener"
                                            >
                                                Visit Website
                                            </a>
                                        </strong>

                                    @else

                                        <strong>—</strong>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>


                    @if ($publisher->address)

                        <div class="entity-note">
                            <strong>Address</strong>
                            <p>{{ $publisher->address }}</p>
                        </div>

                    @endif

                </section>


                {{-- Authors --}}
                <section class="card entity-details-card">

                    <div class="entity-details-header">

                        <div>
                            <span class="eyebrow">
                                Creators
                            </span>

                            <h2>
                                Authors
                            </h2>
                        </div>


                        @can('manageAuthors', $publisher)

                            <a
                                href="{{ route(
                                    'authors.create',
                                    ['publisher' => $publisher->id]
                                ) }}"
                                class="btn btn--secondary"
                            >
                                Add Author
                            </a>

                        @endcan

                    </div>


                    @if ($publisher->authors->count())

                        <div class="entity-list">

                            @foreach ($publisher->authors as $author)

                                <a
                                    href="{{ route(
                                        'authors.show',
                                        $author
                                    ) }}"
                                    class="entity-list-item"
                                >

                                    <span class="directory-avatar">
                                        {{ collect(
                                            preg_split(
                                                '/\s+/',
                                                trim($author->name)
                                            )
                                        )
                                            ->filter()
                                            ->take(2)
                                            ->map(
                                                fn ($part) =>
                                                    strtoupper(
                                                        mb_substr(
                                                            $part,
                                                            0,
                                                            1
                                                        )
                                                    )
                                            )
                                            ->implode('')
                                        }}
                                    </span>


                                    <div>
                                        <strong>
                                            {{ $author->name }}
                                        </strong>

                                        <span>
                                            {{ str($author->status)->title() }}
                                        </span>
                                    </div>

                                </a>

                            @endforeach

                        </div>

                    @else

                        <div class="empty-inline">
                            No authors are currently registered under this publisher.
                        </div>

                    @endif

                </section>


                {{-- Books --}}
                <section class="card entity-details-card">

                    <div class="entity-details-header">

                        <div>
                            <span class="eyebrow">
                                Catalogue
                            </span>

                            <h2>
                                Books
                            </h2>
                        </div>


                        @if (Route::has('books.create'))

                            <a
                                href="{{ route(
                                    'books.create',
                                    ['publisher' => $publisher->id]
                                ) }}"
                                class="btn btn--secondary"
                            >
                                Add Book
                            </a>

                        @endif

                    </div>


                    @if ($publisher->books->count())

                        <div class="table-wrapper">

                            <table class="table-condensed">

                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>ISBN</th>
                                        <th>Authors</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>


                                <tbody>

                                    @foreach ($publisher->books as $book)

                                        <tr>

                                            <td>
                                                <a
                                                    href="{{ route(
                                                        'books.show',
                                                        $book
                                                    ) }}"
                                                    class="table-link"
                                                >
                                                    {{ $book->title }}
                                                </a>
                                            </td>

                                            <td>
                                                {{ $book->isbn }}
                                            </td>

                                            <td>
                                                {{ $book->authors
                                                    ->pluck('name')
                                                    ->join(', ')
                                                    ?: '—'
                                                }}
                                            </td>

                                            <td>
                                                <x-library.book-status
                                                    :status="$book->status"
                                                />
                                            </td>

                                            <td>
                                                <a
                                                    href="{{ route(
                                                        'books.show',
                                                        $book
                                                    ) }}"
                                                    class="table-icon-button"
                                                >
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                                        <circle cx="12" cy="12" r="3"/>
                                                    </svg>
                                                </a>
                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                        <div class="empty-inline">
                            This publisher has no books in the catalogue.
                        </div>

                    @endif

                </section>


                {{-- Licences --}}
                <section class="card entity-details-card">

                    <div class="entity-details-header">

                        <div>
                            <span class="eyebrow">
                                Distribution
                            </span>

                            <h2>
                                Institutional Licences
                            </h2>
                        </div>

                    </div>


                    @if ($publisher->schoolBookLicenses->count())

                        <div class="table-wrapper">

                            <table class="table-condensed">

                                <thead>
                                    <tr>
                                        <th>Licence</th>
                                        <th>School</th>
                                        <th>Book</th>
                                        <th>Expires</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>


                                <tbody>

                                    @foreach ($publisher->schoolBookLicenses as $license)

                                        <tr>

                                            <td>
                                                <a
                                                    href="{{ route(
                                                        'book-licenses.show',
                                                        $license
                                                    ) }}"
                                                >
                                                    {{ $license->license_number }}
                                                </a>
                                            </td>

                                            <td>
                                                {{ $license->school?->name }}
                                            </td>

                                            <td>
                                                {{ $license->book?->title }}
                                            </td>

                                            <td>
                                                {{ $license->expires_at
                                                    ?->format('d M Y')
                                                    ?? 'No expiry'
                                                }}
                                            </td>

                                            <td>
                                                <x-library.license-status
                                                    :status="$license->status"
                                                />
                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else

                        <div class="empty-inline">
                            No licences have been issued directly by this publisher.
                        </div>

                    @endif

                </section>

            </div>


            <aside class="entity-details-sidebar">

                <section class="card entity-stat-card">

                    <span class="eyebrow">
                        Publisher
                    </span>

                    <h2>
                        Catalogue Summary
                    </h2>


                    <div class="entity-stat-row">
                        <span>Authors</span>
                        <strong>
                            {{ number_format($publisher->authors->count()) }}
                        </strong>
                    </div>

                    <div class="entity-stat-row">
                        <span>Books</span>
                        <strong>
                            {{ number_format($publisher->books->count()) }}
                        </strong>
                    </div>

                    <div class="entity-stat-row">
                        <span>Licences</span>
                        <strong>
                            {{ number_format(
                                $publisher->schoolBookLicenses->count()
                            ) }}
                        </strong>
                    </div>

                </section>


                @can('delete', $publisher)

                    <section class="card entity-danger-card">

                        <strong>
                            Delete Publisher
                        </strong>

                        <p>
                            This is only available where the publisher
                            has no dependent books or licence records.
                        </p>


                        <form
                            method="POST"
                            action="{{ route(
                                'publishers.destroy',
                                $publisher
                            ) }}"
                            onsubmit="
                                return confirm(
                                    'Delete this publisher?'
                                );
                            "
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn--danger btn--block"
                            >
                                Delete Publisher
                            </button>

                        </form>

                    </section>

                @endcan

            </aside>

        </div>

    </div>


    <style>
        .entity-details-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 16px;
            align-items: start;
        }

        .entity-details-main,
        .entity-details-sidebar {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .entity-details-card,
        .entity-stat-card,
        .entity-danger-card {
            padding: 18px;
        }

        .entity-details-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 15px;
        }

        .entity-details-header h2,
        .entity-stat-card h2 {
            margin: 2px 0 0;
            font-size: .88rem;
        }

        .publisher-profile {
            display: grid;
            grid-template-columns: 110px 1fr;
            gap: 18px;
        }

        .publisher-logo {
            width: 110px;
            height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            background: var(--color-surface-soft);
            color: var(--color-primary);
            font-size: 1.3rem;
            font-weight: 800;
        }

        .publisher-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .publisher-profile__body h3 {
            margin: 0;
            font-size: 1rem;
        }

        .publisher-profile__body > p {
            color: var(--color-text-muted);
            font-size: .63rem;
            line-height: 1.65;
        }

        .entity-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .entity-meta-grid div {
            display: flex;
            flex-direction: column;
        }

        .entity-meta-grid span {
            color: var(--color-text-muted);
            font-size: .53rem;
        }

        .entity-meta-grid strong {
            margin-top: 2px;
            font-size: .61rem;
        }

        .entity-note {
            margin-top: 15px;
            padding: 12px;
            border-radius: var(--radius-md);
            background: var(--color-surface-soft);
        }

        .entity-note strong {
            font-size: .61rem;
        }

        .entity-note p {
            margin: 4px 0 0;
            color: var(--color-text-muted);
            font-size: .58rem;
        }

        .entity-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .entity-list-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            color: inherit;
            text-decoration: none;
        }

        .entity-list-item:hover {
            background: var(--color-surface-soft);
        }

        .entity-list-item > div {
            display: flex;
            flex-direction: column;
        }

        .entity-list-item strong {
            font-size: .63rem;
        }

        .entity-list-item span:last-child {
            color: var(--color-text-muted);
            font-size: .53rem;
        }

        .entity-stat-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--color-border);
        }

        .entity-stat-row:last-child {
            border-bottom: 0;
        }

        .entity-stat-row span {
            color: var(--color-text-muted);
            font-size: .56rem;
        }

        .entity-stat-row strong {
            font-size: .65rem;
        }

        .entity-danger-card > strong {
            font-size: .64rem;
        }

        .entity-danger-card p {
            color: var(--color-text-muted);
            font-size: .56rem;
            line-height: 1.5;
        }

        .btn--block {
            width: 100%;
            justify-content: center;
        }

        @media (max-width: 900px) {
            .entity-details-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .publisher-profile {
                grid-template-columns: 1fr;
            }

            .entity-list,
            .entity-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

</x-layouts.dashboard>