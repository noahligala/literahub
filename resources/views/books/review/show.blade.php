<x-layouts.dashboard>

    <div class="page-shell">

        {{-- ================================================================
             HEADER
        ================================================================= --}}

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Content Review
                </span>

                <h1>
                    {{ $book->title }}
                </h1>

                <p>
                    Review metadata, authorship, digital rights
                    and the uploaded manuscript before publication.
                </p>

            </div>


            <div class="page-header__actions">

                <a
                    href="{{ route('book-reviews.index') }}"
                    class="btn btn--secondary"
                >
                    Review Queue
                </a>


                <a
                    href="{{ route('books.show', $book) }}"
                    class="btn btn--secondary"
                >
                    Book Record
                </a>


                @if (
                    in_array(
                        $book->status,
                        [
                            'under_review',
                            'approved',
                        ],
                        true
                    )
                )

                    <a
                        href="{{ route('reader.show', $book) }}"
                        class="btn btn--primary"
                    >
                        Preview Book
                    </a>

                @endif

            </div>

        </div>


        <div class="review-layout">


            {{-- ============================================================
                 MAIN REVIEW
            ============================================================= --}}

            <div class="review-main">


                {{-- ========================================================
                     CATALOGUE DETAILS
                ========================================================= --}}

                <section class="card review-section">

                    <div class="review-section__header">

                        <div>

                            <span class="eyebrow">
                                Metadata
                            </span>

                            <h2>
                                Catalogue Information
                            </h2>

                        </div>


                        <x-library.book-status
                            :status="$book->status"
                        />

                    </div>


                    <div class="review-book-summary">

                        <div class="review-cover">

                            @if ($book->cover_path)

                                <img
                                    src="{{ asset(
                                        'storage/'
                                        . $book->cover_path
                                    ) }}"
                                    alt="{{ $book->title }}"
                                >

                            @else

                                <div class="review-cover__placeholder">

                                    <svg viewBox="0 0 24 24">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                                    </svg>

                                </div>

                            @endif

                        </div>


                        <div class="review-book-summary__body">

                            <h3>
                                {{ $book->title }}
                            </h3>

                            <p class="review-authors">

                                {{ $book->authors
                                    ->pluck('name')
                                    ->join(', ')
                                    ?: 'No author assigned'
                                }}

                            </p>


                            <div class="review-meta-grid">

                                <div>
                                    <span>ISBN</span>
                                    <strong>
                                        {{ $book->isbn }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Publisher</span>
                                    <strong>
                                        {{ $book->publisher?->name
                                            ?? 'Independent'
                                        }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Category</span>
                                    <strong>
                                        {{ $book->category ?? '—' }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Language</span>
                                    <strong>
                                        {{ $book->language }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Edition</span>
                                    <strong>
                                        {{ $book->edition ?? '—' }}
                                    </strong>
                                </div>

                                <div>
                                    <span>Year</span>
                                    <strong>
                                        {{ $book->publication_year ?? '—' }}
                                    </strong>
                                </div>

                            </div>

                        </div>

                    </div>


                    @if ($book->description)

                        <div class="review-description">

                            <strong>
                                Description
                            </strong>

                            <p>
                                {{ $book->description }}
                            </p>

                        </div>

                    @endif

                </section>


                {{-- ========================================================
                     AUTHORS
                ========================================================= --}}

                <section class="card review-section">

                    <div class="review-section__header">

                        <div>

                            <span class="eyebrow">
                                Ownership
                            </span>

                            <h2>
                                Authors & Rights Holder
                            </h2>

                        </div>

                    </div>


                    <div class="review-author-list">

                        @forelse ($book->authors as $author)

                            <div class="review-author">

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
                                            fn ($word) =>
                                                strtoupper(
                                                    mb_substr(
                                                        $word,
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
                                        {{ $author->publisher?->name
                                            ?? 'Independent author'
                                        }}
                                    </span>

                                </div>

                            </div>

                        @empty

                            <div class="empty-inline">
                                No authors are associated with this work.
                            </div>

                        @endforelse

                    </div>


                    @if ($book->rights_statement)

                        <div class="review-note-box">

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
                     PERMISSIONS
                ========================================================= --}}

                <section class="card review-section">

                    <div class="review-section__header">

                        <div>

                            <span class="eyebrow">
                                Distribution
                            </span>

                            <h2>
                                Rights & Permissions
                            </h2>

                        </div>

                    </div>


                    <x-library.book-rights
                        :book="$book"
                    />

                </section>


                {{-- ========================================================
                     DIGITAL FILE
                ========================================================= --}}

                <section class="card review-section">

                    <div class="review-section__header">

                        <div>

                            <span class="eyebrow">
                                Manuscript
                            </span>

                            <h2>
                                Digital File
                            </h2>

                        </div>


                        <a
                            href="{{ route('reader.show', $book) }}"
                            class="btn btn--secondary"
                        >
                            Open Protected Reader
                        </a>

                    </div>


                    <div class="file-inspection">

                        <div>
                            <span>Pages</span>
                            <strong>
                                {{ $book->page_count ?? '—' }}
                            </strong>
                        </div>

                        <div>
                            <span>File Size</span>
                            <strong>

                                @if ($book->file_size)

                                    {{ number_format(
                                        $book->file_size
                                        / 1024
                                        / 1024,
                                        2
                                    ) }} MB

                                @else
                                    —
                                @endif

                            </strong>
                        </div>

                        <div>
                            <span>Uploaded By</span>
                            <strong>
                                {{ $book->uploader?->name ?? '—' }}
                            </strong>
                        </div>

                        <div>
                            <span>Submitted</span>
                            <strong>
                                {{ $book->submitted_at
                                    ?->format('d M Y H:i')
                                    ?? '—'
                                }}
                            </strong>
                        </div>

                    </div>

                </section>


                {{-- ========================================================
                     EXISTING REVIEW
                ========================================================= --}}

                @if (
                    $book->reviewed_at
                    || $book->review_notes
                )

                    <section class="card review-section">

                        <div class="review-section__header">

                            <div>

                                <span class="eyebrow">
                                    Review History
                                </span>

                                <h2>
                                    Previous Decision
                                </h2>

                            </div>

                        </div>


                        <div class="review-history">

                            <div>
                                <span>Reviewed By</span>
                                <strong>
                                    {{ $book->reviewer?->name ?? '—' }}
                                </strong>
                            </div>

                            <div>
                                <span>Reviewed At</span>
                                <strong>
                                    {{ $book->reviewed_at
                                        ?->format('d M Y H:i')
                                        ?? '—'
                                    }}
                                </strong>
                            </div>

                        </div>


                        @if ($book->review_notes)

                            <div class="review-note-box">

                                <strong>
                                    Review Notes
                                </strong>

                                <p>
                                    {{ $book->review_notes }}
                                </p>

                            </div>

                        @endif

                    </section>

                @endif

            </div>


            {{-- ============================================================
                 DECISION SIDEBAR
            ============================================================= --}}

            <aside class="review-sidebar">

                <section class="card decision-card">

                    <span class="eyebrow">
                        Moderation
                    </span>

                    <h2>
                        Review Decision
                    </h2>

                    <p>
                        Choose the appropriate action after reviewing
                        the manuscript and its distribution rights.
                    </p>


                    {{-- ====================================================
                         APPROVE
                    ===================================================== --}}

                    @if ($book->status === 'under_review')

                        <form
                            method="POST"
                            action="{{ route(
                                'book-reviews.approve',
                                $book
                            ) }}"
                        >

                            @csrf
                            @method('PATCH')


                            <div class="form-group">

                                <label for="approval_notes">
                                    Approval Notes
                                </label>

                                <textarea
                                    id="approval_notes"
                                    name="review_notes"
                                    rows="3"
                                    placeholder="Optional reviewer notes..."
                                ></textarea>

                            </div>


                            <button
                                type="submit"
                                class="btn btn--primary btn--block"
                            >
                                Approve Book
                            </button>

                        </form>


                        <div class="decision-divider">
                            or
                        </div>


                        {{-- ================================================
                             REQUEST CHANGES
                        ================================================= --}}

                        <form
                            method="POST"
                            action="{{ route(
                                'book-reviews.request-changes',
                                $book
                            ) }}"
                        >

                            @csrf
                            @method('PATCH')


                            <div class="form-group">

                                <label for="change_notes">
                                    Required Changes
                                </label>

                                <textarea
                                    id="change_notes"
                                    name="review_notes"
                                    rows="4"
                                    required
                                    placeholder="Explain exactly what must be corrected..."
                                ></textarea>

                            </div>


                            <button
                                type="submit"
                                class="btn btn--secondary btn--block"
                            >
                                Request Changes
                            </button>

                        </form>


                        <div class="decision-divider">
                            or
                        </div>


                        {{-- ================================================
                             REJECT
                        ================================================= --}}

                        <form
                            method="POST"
                            action="{{ route(
                                'book-reviews.reject',
                                $book
                            ) }}"
                            onsubmit="
                                return confirm(
                                    'Reject this book?'
                                );
                            "
                        >

                            @csrf
                            @method('PATCH')


                            <div class="form-group">

                                <label for="rejection_notes">
                                    Rejection Reason
                                </label>

                                <textarea
                                    id="rejection_notes"
                                    name="review_notes"
                                    rows="4"
                                    required
                                    placeholder="Explain why the book is being rejected..."
                                ></textarea>

                            </div>


                            <button
                                type="submit"
                                class="btn btn--danger btn--block"
                            >
                                Reject Book
                            </button>

                        </form>

                    @endif


                    {{-- ====================================================
                         PUBLISH
                    ===================================================== --}}

                    @if ($book->status === 'approved')

                        <div class="decision-ready">

                            <strong>
                                Ready for publication
                            </strong>

                            <p>
                                This title has passed review and can now
                                be published to the global LiteraHub catalogue.
                            </p>

                        </div>


                        <form
                            method="POST"
                            action="{{ route(
                                'book-reviews.publish',
                                $book
                            ) }}"
                        >

                            @csrf
                            @method('PATCH')


                            <button
                                type="submit"
                                class="btn btn--primary btn--block"
                            >
                                Publish Book
                            </button>

                        </form>

                    @endif


                    {{-- ====================================================
                         PUBLISHED
                    ===================================================== --}}

                    @if ($book->status === 'published')

                        <div class="decision-complete">

                            <strong>
                                Published
                            </strong>

                            <p>
                                This work is available for institutional
                                licensing.
                            </p>

                        </div>

                    @endif


                    {{-- ====================================================
                         CHANGES REQUESTED
                    ===================================================== --}}

                    @if (
                        $book->status
                        === 'changes_requested'
                    )

                        <div class="decision-warning">

                            <strong>
                                Awaiting author revision
                            </strong>

                            <p>
                                The author must update and resubmit the
                                work before review can continue.
                            </p>

                        </div>

                    @endif


                    {{-- ====================================================
                         REJECTED
                    ===================================================== --}}

                    @if ($book->status === 'rejected')

                        <div class="decision-danger">

                            <strong>
                                Rejected
                            </strong>

                            <p>
                                This work is not approved for publication.
                            </p>

                        </div>

                    @endif

                </section>

            </aside>

        </div>

    </div>


    <style>

        .review-layout {
            display: grid;
            grid-template-columns:
                minmax(0, 1fr)
                300px;
            gap: 16px;
            align-items: start;
        }

        .review-main {
            display: flex;
            flex-direction: column;
            gap: 14px;
            min-width: 0;
        }

        .review-sidebar {
            position: sticky;
            top: 88px;
        }

        .review-section {
            padding: 18px;
        }

        .review-section__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 15px;
        }

        .review-section__header h2 {
            margin: 2px 0 0;
            color: var(--color-text);
            font-size: .88rem;
        }

        .review-book-summary {
            display: grid;
            grid-template-columns: 110px 1fr;
            gap: 16px;
        }

        .review-cover {
            width: 110px;
            aspect-ratio: 2 / 3;
            overflow: hidden;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            background: var(--color-surface-soft);
        }

        .review-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .review-cover__placeholder {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .review-cover__placeholder svg {
            width: 30px;
            height: 30px;
            fill: none;
            stroke: var(--color-primary);
            stroke-width: 1.5;
        }

        .review-book-summary h3 {
            margin: 0;
            color: var(--color-text);
            font-size: 1rem;
        }

        .review-authors {
            margin: 3px 0 14px;
            color: var(--color-text-muted);
            font-size: .62rem;
        }

        .review-meta-grid {
            display: grid;
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
            gap: 9px;
        }

        .review-meta-grid div,
        .file-inspection div,
        .review-history div {
            display: flex;
            flex-direction: column;
        }

        .review-meta-grid span,
        .file-inspection span,
        .review-history span {
            color: var(--color-text-muted);
            font-size: .53rem;
        }

        .review-meta-grid strong,
        .file-inspection strong,
        .review-history strong {
            margin-top: 2px;
            color: var(--color-text);
            font-size: .61rem;
        }

        .review-description {
            margin-top: 15px;
            padding-top: 14px;
            border-top: 1px solid var(--color-border);
        }

        .review-description strong {
            color: var(--color-text);
            font-size: .62rem;
        }

        .review-description p {
            margin: 4px 0 0;
            color: var(--color-text-muted);
            font-size: .62rem;
            line-height: 1.65;
        }

        .review-author-list {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .review-author {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
        }

        .review-author > div {
            display: flex;
            flex-direction: column;
        }

        .review-author strong {
            font-size: .63rem;
        }

        .review-author span:last-child {
            margin-top: 1px;
            color: var(--color-text-muted);
            font-size: .53rem;
        }

        .review-note-box {
            margin-top: 14px;
            padding: 12px;
            border-radius: var(--radius-md);
            background: var(--color-surface-soft);
        }

        .review-note-box strong {
            font-size: .61rem;
        }

        .review-note-box p {
            margin: 4px 0 0;
            color: var(--color-text-muted);
            font-size: .59rem;
            line-height: 1.6;
        }

        .file-inspection,
        .review-history {
            display: grid;
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .decision-card {
            padding: 17px;
        }

        .decision-card h2 {
            margin: 3px 0 5px;
            font-size: .9rem;
        }

        .decision-card > p {
            margin: 0 0 15px;
            color: var(--color-text-muted);
            font-size: .59rem;
            line-height: 1.5;
        }

        .decision-card .form-group {
            margin-bottom: 9px;
        }

        .btn--block {
            width: 100%;
            justify-content: center;
        }

        .decision-divider {
            margin: 14px 0;
            color: var(--color-text-muted);
            font-size: .53rem;
            text-align: center;
            text-transform: uppercase;
        }

        .decision-ready,
        .decision-complete,
        .decision-warning,
        .decision-danger {
            margin-bottom: 13px;
            padding: 11px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            background: var(--color-surface-soft);
        }

        .decision-ready strong,
        .decision-complete strong,
        .decision-warning strong,
        .decision-danger strong {
            font-size: .63rem;
        }

        .decision-ready p,
        .decision-complete p,
        .decision-warning p,
        .decision-danger p {
            margin: 3px 0 0;
            color: var(--color-text-muted);
            font-size: .55rem;
            line-height: 1.5;
        }

        @media (max-width: 900px) {

            .review-layout {
                grid-template-columns: 1fr;
            }

            .review-sidebar {
                position: static;
            }

        }

        @media (max-width: 650px) {

            .review-book-summary {
                grid-template-columns: 1fr;
            }

            .review-meta-grid,
            .file-inspection,
            .review-history,
            .review-author-list {
                grid-template-columns: 1fr 1fr;
            }

        }

        @media (max-width: 480px) {

            .review-meta-grid,
            .file-inspection,
            .review-history,
            .review-author-list {
                grid-template-columns: 1fr;
            }

        }

    </style>

</x-layouts.dashboard>