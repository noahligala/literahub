<x-layouts.dashboard>

    <div class="page-shell">

        {{-- ================================================================
             HEADER
        ================================================================= --}}

        <div class="page-header">

            <div>
                <span class="eyebrow">
                    Digital Catalogue
                </span>

                <h1>
                    Books
                </h1>

                <p>
                    Manage LiteraHub titles, authors, publishers,
                    review status and distribution rights.
                </p>
            </div>

            @can('create', App\Models\Book::class)

                <a
                    href="{{ route('books.create') }}"
                    class="btn btn--primary"
                >
                    Add Book
                </a>

            @endcan

        </div>


        {{-- ================================================================
             FILTERS
        ================================================================= --}}

        <div class="card">

            <form
                method="GET"
                action="{{ route('books.index') }}"
                class="catalogue-filters"
            >

                <div class="filter-search">

                    <label
                        for="search"
                        class="sr-only"
                    >
                        Search books
                    </label>

                    <input
                        id="search"
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search title, ISBN or author..."
                    >

                </div>


                <div>

                    <label
                        for="status"
                        class="sr-only"
                    >
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                    >

                        <option value="">
                            All statuses
                        </option>

                        @foreach ([
                            'draft' => 'Draft',
                            'under_review' => 'Under Review',
                            'changes_requested' => 'Changes Requested',
                            'approved' => 'Approved',
                            'published' => 'Published',
                            'rejected' => 'Rejected',
                            'archived' => 'Archived',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(request('status') === $value)
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <button
                    type="submit"
                    class="btn btn--secondary"
                >
                    Filter
                </button>


                @if (
                    request('search')
                    || request('status')
                )

                    <a
                        href="{{ route('books.index') }}"
                        class="btn btn--ghost"
                    >
                        Clear
                    </a>

                @endif

            </form>

        </div>


        {{-- ================================================================
             TABLE
        ================================================================= --}}

        <div class="card">

            @if ($books->count())

                <div class="table-wrapper">

                    <table class="table-condensed">

                        <thead>

                            <tr>
                                <th>Book</th>
                                <th>ISBN</th>
                                <th>Publisher</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Rights</th>
                                <th>Updated</th>
                                <th></th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($books as $book)

                                @php
                                    $bookInitials = collect(
                                        preg_split(
                                            '/\s+/',
                                            trim($book->title)
                                        )
                                    )
                                        ->filter()
                                        ->take(2)
                                        ->map(
                                            fn ($word) =>
                                                strtoupper(
                                                    mb_substr(
                                                        $word,
                                                        0,
                                                        1
                                                    )
                                                )
                                        )
                                        ->implode('');
                                @endphp


                                <tr>

                                    {{-- Book --}}
                                    <td>

                                        <div class="directory-person">

                                            <span class="directory-avatar">
                                                {{ $bookInitials }}
                                            </span>


                                            <div class="directory-person__details">

                                                <div class="directory-person__name">

                                                    <a
                                                        href="{{ route('books.show', $book) }}"
                                                    >
                                                        {{ $book->title }}
                                                    </a>

                                                </div>

                                                <span class="directory-person__meta">

                                                    {{ $book->authors->pluck('name')->join(', ') ?: 'No author assigned' }}

                                                </span>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- ISBN --}}
                                    <td>

                                        <span class="table-value">
                                            {{ $book->isbn }}
                                        </span>

                                    </td>


                                    {{-- Publisher --}}
                                    <td>

                                        <span class="table-value">
                                            {{ $book->publisher?->name ?? 'Independent' }}
                                        </span>

                                    </td>


                                    {{-- Category --}}
                                    <td>

                                        <span class="table-value">
                                            {{ $book->category ?? '—' }}
                                        </span>

                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        <x-library.book-status
                                            :status="$book->status"
                                        />

                                    </td>


                                    {{-- Rights --}}
                                    <td>

                                        <div class="mini-rights">

                                            @if ($book->allow_online_reading)
                                                <span title="Online reading allowed">
                                                    Read
                                                </span>
                                            @endif

                                            @if ($book->allow_print)
                                                <span title="Printing allowed">
                                                    Print
                                                </span>
                                            @endif

                                            @if ($book->allow_download)
                                                <span title="Download allowed">
                                                    Download
                                                </span>
                                            @endif

                                        </div>

                                    </td>


                                    {{-- Updated --}}
                                    <td>

                                        <span class="table-value">
                                            {{ $book->updated_at?->format('d M Y') }}
                                        </span>

                                    </td>


                                    {{-- Actions --}}
                                    <td>

                                        <div class="table-icon-actions">

                                            <a
                                                href="{{ route('books.show', $book) }}"
                                                class="table-icon-button"
                                                title="View book"
                                                aria-label="View book"
                                            >
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </a>


                                            @can('update', $book)

                                                <a
                                                    href="{{ route('books.edit', $book) }}"
                                                    class="table-icon-button"
                                                    title="Edit book"
                                                    aria-label="Edit book"
                                                >
                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M12 20h9"/>
                                                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                                    </svg>
                                                </a>

                                            @endcan


                                            @can('delete', $book)

                                                <form
                                                    method="POST"
                                                    action="{{ route('books.destroy', $book) }}"
                                                    class="table-icon-form"
                                                    onsubmit="return confirm('Delete this book? This action cannot be undone.');"
                                                >

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="table-icon-button table-icon-button--danger"
                                                        title="Delete book"
                                                        aria-label="Delete book"
                                                    >
                                                        <svg viewBox="0 0 24 24">
                                                            <path d="M3 6h18"/>
                                                            <path d="M8 6V4h8v2"/>
                                                            <path d="M19 6l-1 14H6L5 6"/>
                                                            <path d="M10 11v5"/>
                                                            <path d="M14 11v5"/>
                                                        </svg>
                                                    </button>

                                                </form>

                                            @endcan

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                <div class="pagination-shell">
                    {{ $books->links() }}
                </div>

            @else

                <div class="empty-state">

                    <div class="empty-state__icon">

                        <svg viewBox="0 0 24 24">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                        </svg>

                    </div>

                    <h2>
                        No books found
                    </h2>

                    <p>
                        Add your first book or adjust the current filters.
                    </p>

                    @can('create', App\Models\Book::class)

                        <a
                            href="{{ route('books.create') }}"
                            class="btn btn--primary"
                        >
                            Add Book
                        </a>

                    @endcan

                </div>

            @endif

        </div>

    </div>


    <style>
        .catalogue-filters {
            display: grid;
            grid-template-columns: minmax(260px, 1fr) 180px auto auto;
            gap: 8px;
            align-items: end;
        }

        .filter-search {
            min-width: 0;
        }

        .catalogue-filters input,
        .catalogue-filters select {
            width: 100%;
        }

        .mini-rights {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
        }

        .mini-rights span {
            padding: 2px 5px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            color: var(--color-text-muted);
            font-size: .52rem;
            font-weight: 700;
        }

        .pagination-shell {
            margin-top: 15px;
        }

        @media (max-width: 800px) {
            .catalogue-filters {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 520px) {
            .catalogue-filters {
                grid-template-columns: 1fr;
            }
        }
    </style>

</x-layouts.dashboard>