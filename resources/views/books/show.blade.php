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
                    {{ $book->title }}
                </h1>

                <p>
                    {{ $book->isbn }}
                    @if ($book->edition)
                        · {{ $book->edition }}
                    @endif
                </p>

            </div>


            <div class="page-header__actions">

                <a
                    href="{{ route('books.index') }}"
                    class="btn btn--secondary"
                >
                    Back
                </a>


                @can('read', $book)

                    <a
                        href="{{ route('reader.show', $book) }}"
                        class="btn btn--secondary"
                    >
                        Preview
                    </a>

                @endcan


                @can('update', $book)

                    <a
                        href="{{ route('books.edit', $book) }}"
                        class="btn btn--primary"
                    >
                        Edit Book
                    </a>

                @endcan

            </div>

        </div>


        <div class="book-details-layout">


            {{-- ============================================================
                 MAIN
            ============================================================= --}}

            <div class="book-details-main">


                {{-- ========================================================
                     OVERVIEW
                ========================================================= --}}

                <section class="card book-overview">

                    <div class="book-cover">

                        @if ($book->cover_path)

                            <img
                                src="{{ asset('storage/' . $book->cover_path) }}"
                                alt="{{ $book->title }}"
                            >

                        @else

                            <div class="book-cover__placeholder">

                                <svg viewBox="0 0 24 24">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                                </svg>

                                <span>
                                    {{ $book->title }}
                                </span>

                            </div>

                        @endif

                    </div>


                    <div class="book-overview__body">

                        <div class="book-overview__top">

                            <x-library.book-status
                                :status="$book->status"
                            />

                            @if ($book->category)

                                <span class="badge">
                                    {{ $book->category }}
                                </span>

                            @endif

                        </div>


                        <h2>
                            {{ $book->title }}
                        </h2>


                        <p class="book-authors">

                            By

                            <strong>
                                {{ $book->authors->pluck('name')->join(', ') ?: 'Unknown Author' }}
                            </strong>

                        </p>


                        @if ($book->description)

                            <p class="book-description">
                                {{ $book->description }}
                            </p>

                        @else

                            <p class="book-description">
                                No description has been provided for this title.
                            </p>

                        @endif


                        <div class="book-meta-grid">

                            <div>
                                <span>Publisher</span>
                                <strong>
                                    {{ $book->publisher?->name ?? 'Independent' }}
                                </strong>
                            </div>

                            <div>
                                <span>Language</span>
                                <strong>
                                    {{ $book->language }}
                                </strong>
                            </div>

                            <div>
                                <span>Published</span>
                                <strong>
                                    {{ $book->publication_year ?? '—' }}
                                </strong>
                            </div>

                            <div>
                                <span>Pages</span>
                                <strong>
                                    {{ $book->page_count ?? '—' }}
                                </strong>
                            </div>

                            <div>
                                <span>Loan Period</span>
                                <strong>
                                    {{ $book->loan_days }} days
                                </strong>
                            </div>

                            <div>
                                <span>Concurrent Loans</span>
                                <strong>
                                    {{ $book->max_concurrent_loans ?? 'Unlimited' }}
                                </strong>
                            </div>

                        </div>

                    </div>

                </section>


                {{-- ========================================================
                     RIGHTS
                ========================================================= --}}

                <section class="card details-section">

                    <div class="details-section__header">

                        <div>

                            <span class="eyebrow">
                                Intellectual Property
                            </span>

                            <h2>
                                Rights & Permissions
                            </h2>

                        </div>

                    </div>


                    <x-library.book-rights
                        :book="$book"
                    />


                    @if ($book->rights_statement)

                        <div class="rights-statement">

                            <strong>
                                Rights Statement
                            </strong>

                            <p>
                                {{ $book->rights_statement }}
                            </p>

                        </div>

                    @endif

                </section>


                {{-- ========================================================
                     REVIEW
                ========================================================= --}}

                <section class="card details-section">

                    <div class="details-section__header">

                        <div>

                            <span class="eyebrow">
                                Workflow
                            </span>

                            <h2>
                                Review History
                            </h2>

                        </div>


                        @can('review', $book)

                            <a
                                href="{{ route('book-reviews.show', $book) }}"
                                class="btn btn--secondary"
                            >
                                Review Book
                            </a>

                        @endcan

                    </div>


                    <div class="review-details">

                        <div>
                            <span>Submitted</span>
                            <strong>
                                {{ $book->submitted_at?->format('d M Y H:i') ?? 'Not submitted' }}
                            </strong>
                        </div>

                        <div>
                            <span>Reviewed</span>
                            <strong>
                                {{ $book->reviewed_at?->format('d M Y H:i') ?? 'Not reviewed' }}
                            </strong>
                        </div>

                        <div>
                            <span>Reviewer</span>
                            <strong>
                                {{ $book->reviewer?->name ?? '—' }}
                            </strong>
                        </div>

                    </div>


                    @if ($book->review_notes)

                        <div class="review-notes">

                            <strong>
                                Review Notes
                            </strong>

                            <p>
                                {{ $book->review_notes }}
                            </p>

                        </div>

                    @endif

                </section>


                {{-- ========================================================
                     LICENCES
                ========================================================= --}}

                <section class="card details-section">

                    <div class="details-section__header">

                        <div>

                            <span class="eyebrow">
                                Distribution
                            </span>

                            <h2>
                                School Licences
                            </h2>

                        </div>


                        @can('create', App\Models\SchoolBookLicense::class)

                            <a
                                href="{{ route('book-licenses.create', [
                                    'book' => $book->id
                                ]) }}"
                                class="btn btn--secondary"
                            >
                                Issue Licence
                            </a>

                        @endcan

                    </div>


                    @if ($book->licenses->count())

                        <div class="table-wrapper">

                            <table class="table-condensed">

                                <thead>

                                    <tr>
                                        <th>School</th>
                                        <th>Licence</th>
                                        <th>Type</th>
                                        <th>Expires</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($book->licenses as $license)

                                        <tr>

                                            <td>
                                                {{ $license->school?->name ?? 'Unknown' }}
                                            </td>

                                            <td>
                                                {{ $license->license_number }}
                                            </td>

                                            <td>
                                                {{ str($license->license_type)->title() }}
                                            </td>

                                            <td>
                                                {{ $license->expires_at?->format('d M Y') ?? 'No expiry' }}
                                            </td>

                                            <td>

                                                <x-library.license-status
                                                    :status="$license->status"
                                                />

                                            </td>

                                            <td>

                                                <a
                                                    href="{{ route('book-licenses.show', $license) }}"
                                                    class="table-icon-button"
                                                    title="View licence"
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
                            No institutional licences have been issued for this title.
                        </div>

                    @endif

                </section>

            </div>


            {{-- ============================================================
                 SIDEBAR
            ============================================================= --}}

            <aside class="book-details-sidebar">

                <section class="card details-sidebar-card">

                    <span class="eyebrow">
                        File
                    </span>

                    <h2>
                        Digital Asset
                    </h2>


                    <dl>

                        <div>
                            <dt>File size</dt>
                            <dd>
                                @if ($book->file_size)
                                    {{ number_format($book->file_size / 1024 / 1024, 2) }} MB
                                @else
                                    —
                                @endif
                            </dd>
                        </div>


                        <div>
                            <dt>Hash</dt>
                            <dd class="hash-value">
                                {{ $book->file_hash
                                    ? Str::limit($book->file_hash, 18)
                                    : '—'
                                }}
                            </dd>
                        </div>


                        <div>
                            <dt>Uploaded by</dt>
                            <dd>
                                {{ $book->uploader?->name ?? '—' }}
                            </dd>
                        </div>

                    </dl>

                </section>


                <section class="card details-sidebar-card">

                    <span class="eyebrow">
                        Status
                    </span>

                    <h2>
                        Catalogue State
                    </h2>

                    <div class="status-large">

                        <x-library.book-status
                            :status="$book->status"
                        />

                    </div>


                    @can('review', $book)

                        <a
                            href="{{ route('book-reviews.show', $book) }}"
                            class="btn btn--primary btn--block"
                        >
                            Open Review
                        </a>

                    @endcan

                </section>

            </aside>

        </div>

    </div>


    <style>
        .book-details-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 16px;
            align-items: start;
        }

        .book-details-main {
            display: flex;
            flex-direction: column;
            gap: 14px;
            min-width: 0;
        }

        .book-details-sidebar {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .book-overview {
            display: grid;
            grid-template-columns: 160px minmax(0, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .book-cover {
            width: 160px;
            aspect-ratio: 2 / 3;
            overflow: hidden;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            background: var(--color-surface-soft);
        }

        .book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .book-cover__placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px;
            text-align: center;
        }

        .book-cover__placeholder svg {
            width: 34px;
            height: 34px;
            fill: none;
            stroke: var(--color-primary);
            stroke-width: 1.5;
        }

        .book-cover__placeholder span {
            color: var(--color-text-muted);
            font-size: .59rem;
            line-height: 1.4;
        }

        .book-overview__top {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .book-overview h2 {
            margin: 11px 0 3px;
            color: var(--color-text);
            font-size: 1.3rem;
        }

        .book-authors {
            margin: 0;
            color: var(--color-text-muted);
            font-size: .68rem;
        }

        .book-description {
            max-width: 800px;
            margin: 15px 0;
            color: var(--color-text-muted);
            font-size: .68rem;
            line-height: 1.7;
        }

        .book-meta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            padding-top: 14px;
            border-top: 1px solid var(--color-border);
        }

        .book-meta-grid div,
        .review-details div {
            display: flex;
            flex-direction: column;
        }

        .book-meta-grid span,
        .review-details span {
            color: var(--color-text-muted);
            font-size: .54rem;
        }

        .book-meta-grid strong,
        .review-details strong {
            margin-top: 2px;
            color: var(--color-text);
            font-size: .64rem;
        }

        .details-section {
            padding: 18px;
        }

        .details-section__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 15px;
        }

        .details-section__header h2 {
            margin: 2px 0 0;
            font-size: .88rem;
        }

        .review-details {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .review-notes,
        .rights-statement {
            margin-top: 15px;
            padding: 12px;
            border-radius: var(--radius-md);
            background: var(--color-surface-soft);
        }

        .review-notes strong,
        .rights-statement strong {
            font-size: .62rem;
        }

        .review-notes p,
        .rights-statement p {
            margin: 4px 0 0;
            color: var(--color-text-muted);
            font-size: .61rem;
            line-height: 1.6;
        }

        .details-sidebar-card {
            padding: 16px;
        }

        .details-sidebar-card h2 {
            margin: 2px 0 12px;
            font-size: .86rem;
        }

        .details-sidebar-card dl {
            margin: 0;
        }

        .details-sidebar-card dl > div {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            padding: 7px 0;
            border-bottom: 1px solid var(--color-border);
        }

        .details-sidebar-card dl > div:last-child {
            border-bottom: 0;
        }

        .details-sidebar-card dt {
            color: var(--color-text-muted);
            font-size: .56rem;
        }

        .details-sidebar-card dd {
            margin: 0;
            color: var(--color-text);
            font-size: .58rem;
            font-weight: 700;
        }

        .hash-value {
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .status-large {
            margin-bottom: 12px;
        }

        .btn--block {
            width: 100%;
            justify-content: center;
        }

        @media (max-width: 900px) {
            .book-details-layout {
                grid-template-columns: 1fr;
            }

            .book-details-sidebar {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 650px) {
            .book-overview {
                grid-template-columns: 1fr;
            }

            .book-cover {
                width: 130px;
            }

            .book-meta-grid,
            .review-details,
            .book-details-sidebar {
                grid-template-columns: 1fr;
            }
        }
    </style>

</x-layouts.dashboard>