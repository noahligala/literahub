<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Author
                </span>

                <h1>
                    {{ $author->name }}
                </h1>

                <p>
                    {{ $author->publisher?->name
                        ?? 'Independent Author'
                    }}
                </p>

            </div>


            <div class="page-header__actions">

                <a
                    href="{{ route('authors.index') }}"
                    class="btn btn--secondary"
                >
                    Back
                </a>


                @can('update', $author)

                    <a
                        href="{{ route(
                            'authors.edit',
                            $author
                        ) }}"
                        class="btn btn--primary"
                    >
                        Edit Author
                    </a>

                @endcan

            </div>

        </div>


        <div class="author-detail-layout">

            <div class="author-detail-main">

                {{-- Profile --}}
                <section class="card author-section">

                    <div class="author-section-header">

                        <div>
                            <span class="eyebrow">
                                Profile
                            </span>

                            <h2>
                                Author Overview
                            </h2>
                        </div>


                        @php
                            $authorStatus = match ($author->status) {
                                'verified' => 'badge--success',
                                'pending' => 'badge--warning',
                                'suspended' => 'badge--danger',
                                default => 'badge--muted',
                            };
                        @endphp

                        <span class="badge {{ $authorStatus }}">
                            {{ str($author->status)->title() }}
                        </span>

                    </div>


                    <div class="author-profile">

                        <div class="author-photo">

                            @if ($author->photo_path)

                                <img
                                    src="{{ asset(
                                        'storage/'
                                        . $author->photo_path
                                    ) }}"
                                    alt="{{ $author->name }}"
                                >

                            @else

                                <span>

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
                                                    mb_substr($part, 0, 1)
                                                )
                                        )
                                        ->implode('')
                                    }}

                                </span>

                            @endif

                        </div>


                        <div class="author-profile__body">

                            <h3>
                                {{ $author->name }}
                            </h3>


                            <p class="author-publisher">

                                @if ($author->publisher)

                                    Published with

                                    <a
                                        href="{{ route(
                                            'publishers.show',
                                            $author->publisher
                                        ) }}"
                                    >
                                        {{ $author->publisher->name }}
                                    </a>

                                @else

                                    Independent author

                                @endif

                            </p>


                            <p class="author-biography">

                                {{ $author->biography
                                    ?: 'No biography has been provided for this author.'
                                }}

                            </p>

                        </div>

                    </div>

                </section>


                {{-- Books --}}
                <section class="card author-section">

                    <div class="author-section-header">

                        <div>
                            <span class="eyebrow">
                                Catalogue
                            </span>

                            <h2>
                                Books
                            </h2>
                        </div>


                        @can('uploadBook', $author)

                            <a
                                href="{{ route(
                                    'books.create',
                                    ['author' => $author->id]
                                ) }}"
                                class="btn btn--secondary"
                            >
                                Upload Book
                            </a>

                        @endcan

                    </div>


                    @if ($author->books->count())

                        <div class="table-wrapper">

                            <table class="table-condensed">

                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Publisher</th>
                                        <th>Contribution</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>


                                <tbody>

                                    @foreach ($author->books as $book)

                                        <tr>

                                            <td>
                                                <a
                                                    href="{{ route(
                                                        'books.show',
                                                        $book
                                                    ) }}"
                                                >
                                                    {{ $book->title }}
                                                </a>

                                                <div class="table-secondary">
                                                    {{ $book->isbn }}
                                                </div>
                                            </td>

                                            <td>
                                                {{ $book->publisher?->name
                                                    ?? 'Independent'
                                                }}
                                            </td>

                                            <td>
                                                {{ str(
                                                    $book->pivot->contribution
                                                    ?? 'author'
                                                )->title() }}
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
                            No catalogue works are associated with this author.
                        </div>

                    @endif

                </section>


                {{-- Licences --}}
                <section class="card author-section">

                    <div class="author-section-header">

                        <div>
                            <span class="eyebrow">
                                Distribution
                            </span>

                            <h2>
                                Author-Issued Licences
                            </h2>
                        </div>

                    </div>


                    @if ($author->schoolBookLicenses->count())

                        <div class="table-wrapper">

                            <table class="table-condensed">

                                <thead>
                                    <tr>
                                        <th>Licence</th>
                                        <th>Book</th>
                                        <th>School</th>
                                        <th>Expires</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>


                                <tbody>

                                    @foreach ($author->schoolBookLicenses as $license)

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
                                                {{ $license->book?->title }}
                                            </td>

                                            <td>
                                                {{ $license->school?->name }}
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
                            No licences have been issued directly by this author.
                        </div>

                    @endif

                </section>

            </div>


            <aside class="author-detail-sidebar">

                <section class="card author-sidebar-card">

                    <span class="eyebrow">
                        Account
                    </span>

                    <h2>
                        Author Identity
                    </h2>


                    <div class="author-sidebar-row">
                        <span>Profile Status</span>
                        <strong>
                            {{ str($author->status)->title() }}
                        </strong>
                    </div>


                    <div class="author-sidebar-row">
                        <span>User Account</span>
                        <strong>
                            {{ $author->user?->email ?? 'Not linked' }}
                        </strong>
                    </div>


                    <div class="author-sidebar-row">
                        <span>Publisher</span>
                        <strong>
                            {{ $author->publisher?->name ?? 'Independent' }}
                        </strong>
                    </div>


                    <div class="author-sidebar-row">
                        <span>Books</span>
                        <strong>
                            {{ number_format($author->books->count()) }}
                        </strong>
                    </div>

                </section>


                @can('delete', $author)

                    <section class="card author-danger-card">

                        <strong>
                            Delete Author
                        </strong>

                        <p>
                            An author profile should only be deleted where
                            it has no catalogue works requiring preservation.
                        </p>


                        <form
                            method="POST"
                            action="{{ route(
                                'authors.destroy',
                                $author
                            ) }}"
                            onsubmit="
                                return confirm(
                                    'Delete this author profile?'
                                );
                            "
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn--danger btn--block"
                            >
                                Delete Author
                            </button>

                        </form>

                    </section>

                @endcan

            </aside>

        </div>

    </div>


    <style>
        .author-detail-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 16px;
            align-items: start;
        }

        .author-detail-main,
        .author-detail-sidebar {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .author-section,
        .author-sidebar-card,
        .author-danger-card {
            padding: 18px;
        }

        .author-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 15px;
        }

        .author-section-header h2,
        .author-sidebar-card h2 {
            margin: 2px 0 0;
            font-size: .88rem;
        }

        .author-profile {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 18px;
        }

        .author-photo {
            width: 120px;
            height: 120px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--color-border);
            border-radius: 50%;
            background: var(--color-surface-soft);
            color: var(--color-primary);
            font-size: 1.25rem;
            font-weight: 800;
        }

        .author-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .author-profile__body h3 {
            margin: 0;
            font-size: 1rem;
        }

        .author-publisher {
            margin: 3px 0 12px;
            color: var(--color-text-muted);
            font-size: .6rem;
        }

        .author-biography {
            color: var(--color-text-muted);
            font-size: .63rem;
            line-height: 1.7;
        }

        .author-sidebar-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid var(--color-border);
        }

        .author-sidebar-row:last-child {
            border-bottom: 0;
        }

        .author-sidebar-row span {
            color: var(--color-text-muted);
            font-size: .54rem;
        }

        .author-sidebar-row strong {
            max-width: 150px;
            font-size: .56rem;
            text-align: right;
            word-break: break-word;
        }

        .author-danger-card > strong {
            font-size: .63rem;
        }

        .author-danger-card p {
            color: var(--color-text-muted);
            font-size: .56rem;
            line-height: 1.5;
        }

        .btn--block {
            width: 100%;
            justify-content: center;
        }

        @media (max-width: 900px) {
            .author-detail-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 580px) {
            .author-profile {
                grid-template-columns: 1fr;
            }
        }
    </style>

</x-layouts.dashboard>