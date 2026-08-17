<x-layouts.dashboard>

    <div class="page-shell">

        {{-- ================================================================
             HEADER
        ================================================================= --}}

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Content Moderation
                </span>

                <h1>
                    Book Review Queue
                </h1>

                <p>
                    Review submitted works, request corrections,
                    approve compliant titles and publish verified content.
                </p>

            </div>


            <a
                href="{{ route('books.index') }}"
                class="btn btn--secondary"
            >
                All Books
            </a>

        </div>


        {{-- ================================================================
             STATUS FILTERS
        ================================================================= --}}

        <div class="review-tabs">

            @php
                $tabs = [
                    'under_review' => 'Under Review',
                    'changes_requested' => 'Changes Requested',
                    'approved' => 'Approved',
                    'published' => 'Published',
                    'rejected' => 'Rejected',
                    'all' => 'All',
                ];

                $currentStatus =
                    request('status', $status ?? 'under_review');
            @endphp


            @foreach ($tabs as $value => $label)

                <a
                    href="{{ route('book-reviews.index', [
                        'status' => $value
                    ]) }}"
                    class="
                        review-tab
                        {{ $currentStatus === $value
                            ? 'review-tab--active'
                            : ''
                        }}
                    "
                >
                    {{ $label }}
                </a>

            @endforeach

        </div>


        {{-- ================================================================
             REVIEW QUEUE
        ================================================================= --}}

        <div class="card">

            @if ($books->count())

                <div class="table-wrapper">

                    <table class="table-condensed">

                        <thead>

                            <tr>
                                <th>Book</th>
                                <th>Publisher</th>
                                <th>ISBN</th>
                                <th>Submitted By</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th></th>
                            </tr>

                        </thead>


                        <tbody>

                            @foreach ($books as $book)

                                @php
                                    $initials = collect(
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
                                                {{ $initials }}
                                            </span>


                                            <div class="directory-person__details">

                                                <div class="directory-person__name">

                                                    <a
                                                        href="{{ route('book-reviews.show', $book) }}"
                                                    >
                                                        {{ $book->title }}
                                                    </a>

                                                </div>


                                                <span class="directory-person__meta">

                                                    {{ $book->authors
                                                        ->pluck('name')
                                                        ->join(', ')
                                                        ?: 'No author'
                                                    }}

                                                </span>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- Publisher --}}
                                    <td>

                                        <span class="table-value">

                                            {{ $book->publisher?->name
                                                ?? 'Independent'
                                            }}

                                        </span>

                                    </td>


                                    {{-- ISBN --}}
                                    <td>

                                        <span class="table-value">
                                            {{ $book->isbn }}
                                        </span>

                                    </td>


                                    {{-- Uploader --}}
                                    <td>

                                        <span class="table-value">

                                            {{ $book->uploader?->name
                                                ?? 'Unknown'
                                            }}

                                        </span>

                                    </td>


                                    {{-- Submitted --}}
                                    <td>

                                        <span class="table-value">

                                            {{ $book->submitted_at
                                                ?->format('d M Y H:i')
                                                ?? '—'
                                            }}

                                        </span>

                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        <x-library.book-status
                                            :status="$book->status"
                                        />

                                    </td>


                                    {{-- Action --}}
                                    <td>

                                        <div class="table-icon-actions">

                                            <a
                                                href="{{ route('book-reviews.show', $book) }}"
                                                class="table-icon-button"
                                                title="Review book"
                                                aria-label="Review book"
                                            >

                                                <svg viewBox="0 0 24 24">
                                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>

                                            </a>

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
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>

                    </div>

                    <h2>
                        Queue clear
                    </h2>

                    <p>
                        There are no books in this review state.
                    </p>

                </div>

            @endif

        </div>

    </div>


    <style>

        .review-tabs {
            display: flex;
            align-items: center;
            gap: 5px;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .review-tab {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 0 11px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            background: var(--color-surface);
            color: var(--color-text-muted);
            font-size: .61rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }

        .review-tab:hover {
            color: var(--color-primary);
            border-color: var(--brand-300);
        }

        .review-tab--active {
            color: var(--color-primary);
            border-color: var(--brand-300);
            background: var(--color-surface-soft);
        }

        .pagination-shell {
            margin-top: 15px;
        }

    </style>

</x-layouts.dashboard>