<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Institutional Library
                </span>

                <h1>
                    {{ $book->title }}
                </h1>

                <p>
                    {{ $book->authors
                        ->pluck('name')
                        ->join(', ')
                    }}
                </p>

            </div>


            <a
                href="{{ route(
                    'school.library.index'
                ) }}"
                class="btn btn--secondary"
            >
                Back to Library
            </a>

        </div>


        <div class="school-book-layout">


            <div class="school-book-main">

                <section class="card school-book-overview">

                    <div class="school-book-cover">

                        @if ($book->cover_path)

                            <img
                                src="{{ asset(
                                    'storage/'
                                    . $book->cover_path
                                ) }}"
                                alt="{{ $book->title }}"
                            >

                        @else

                            <div class="school-cover-placeholder">

                                <svg viewBox="0 0 24 24">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                                </svg>

                            </div>

                        @endif

                    </div>


                    <div class="school-book-information">

                        <div class="school-book-badges">

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


                        <p class="school-book-author">
                            By
                            <strong>
                                {{ $book->authors
                                    ->pluck('name')
                                    ->join(', ')
                                }}
                            </strong>
                        </p>


                        <p class="school-book-description">
                            {{ $book->description
                                ?: 'No description available.'
                            }}
                        </p>


                        <div class="school-book-meta">

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
                                <span>Language</span>
                                <strong>
                                    {{ $book->language }}
                                </strong>
                            </div>

                            <div>
                                <span>Loan Period</span>
                                <strong>
                                    {{ $book->loan_days }} days
                                </strong>
                            </div>

                        </div>

                    </div>

                </section>


                <section class="card school-book-section">

                    <span class="eyebrow">
                        Distribution
                    </span>

                    <h2>
                        Your Institution's Rights
                    </h2>


                    <x-library.book-rights
                        :book="$book"
                        :license="$license"
                    />

                </section>

            </div>


            <aside class="school-book-sidebar">

                <section class="card school-action-card">

                    <span class="eyebrow">
                        Access
                    </span>

                    <h2>
                        Reading Options
                    </h2>


                    @if ($canRead)

                        <a
                            href="{{ route(
                                'reader.show',
                                $book
                            ) }}"
                            class="btn btn--primary btn--block"
                        >
                            Read Online
                        </a>

                    @elseif (
                        auth()->user()
                            ->hasRole('student')
                    )

                        <p>
                            This title is licensed to your school but
                            has not been assigned to your class.
                        </p>


                        <form
                            method="POST"
                            action="{{ route(
                                'school.library.requests.store',
                                $book
                            ) }}"
                        >

                            @csrf


                            <div class="form-group">

                                <label for="reason">
                                    Reason for Access
                                </label>

                                <textarea
                                    id="reason"
                                    name="reason"
                                    rows="4"
                                    required
                                    placeholder="Explain why you need access to this title..."
                                ></textarea>

                            </div>


                            <button
                                type="submit"
                                class="btn btn--primary btn--block"
                            >
                                Request Access
                            </button>

                        </form>

                    @endif


                    @if (
                        auth()->user()
                            ->hasRole('student')
                    )

                        @if ($borrowing)

                            <div class="loan-status">

                                <span>
                                    Borrowed
                                </span>

                                <strong>
                                    Due
                                    {{ $borrowing->due_at
                                        ?->format('d M Y')
                                    }}
                                </strong>

                            </div>


                            <form
                                method="POST"
                                action="{{ route(
                                    'school.library.return',
                                    $book
                                ) }}"
                            >

                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="btn btn--secondary btn--block"
                                >
                                    Return Book
                                </button>

                            </form>

                        @elseif ($canBorrow)

                            <form
                                method="POST"
                                action="{{ route(
                                    'school.library.borrow',
                                    $book
                                ) }}"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="btn btn--secondary btn--block"
                                >
                                    Borrow Book
                                </button>

                            </form>

                        @endif

                    @endif


                    @if ($canDownload)

                        <a
                            href="{{ route(
                                'reader.download',
                                $book
                            ) }}"
                            class="btn btn--secondary btn--block"
                        >
                            Download
                        </a>

                    @endif


                    @if ($canPrint)

                        <a
                            href="{{ route(
                                'reader.print',
                                $book
                            ) }}"
                            target="_blank"
                            class="btn btn--secondary btn--block"
                        >
                            Print
                        </a>

                    @endif

                </section>


                <section class="card school-action-card">

                    <span class="eyebrow">
                        Licence
                    </span>

                    <h2>
                        School Entitlement
                    </h2>


                    <dl>

                        <div>
                            <dt>Status</dt>
                            <dd>
                                <x-library.license-status
                                    :status="$license->status"
                                />
                            </dd>
                        </div>

                        <div>
                            <dt>Type</dt>
                            <dd>
                                {{ str(
                                    $license->license_type
                                )->title() }}
                            </dd>
                        </div>

                        <div>
                            <dt>Expires</dt>
                            <dd>
                                {{ $license->expires_at
                                    ?->format('d M Y')
                                    ?? 'No expiry'
                                }}
                            </dd>
                        </div>

                    </dl>

                </section>

            </aside>

        </div>

    </div>


    <style>

        .school-book-layout {
            display: grid;
            grid-template-columns:
                minmax(0, 1fr)
                280px;
            gap: 16px;
            align-items: start;
        }

        .school-book-main,
        .school-book-sidebar {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .school-book-overview {
            display: grid;
            grid-template-columns:
                170px
                minmax(0, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .school-book-cover {
            width: 170px;
            aspect-ratio: 2 / 3;
            overflow: hidden;
            border-radius: var(--radius-md);
            background: var(--color-surface-soft);
        }

        .school-book-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .school-cover-placeholder {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .school-cover-placeholder svg {
            width: 40px;
            height: 40px;
            fill: none;
            stroke: var(--color-primary);
            stroke-width: 1.5;
        }

        .school-book-badges {
            display: flex;
            gap: 5px;
        }

        .school-book-information h2 {
            margin: 11px 0 3px;
            font-size: 1.25rem;
        }

        .school-book-author {
            margin: 0;
            color: var(--color-text-muted);
            font-size: .63rem;
        }

        .school-book-description {
            margin: 16px 0;
            color: var(--color-text-muted);
            font-size: .64rem;
            line-height: 1.7;
        }

        .school-book-meta {
            display: grid;
            grid-template-columns:
                repeat(2, 1fr);
            gap: 10px;
            padding-top: 14px;
            border-top: 1px solid var(--color-border);
        }

        .school-book-meta div {
            display: flex;
            flex-direction: column;
        }

        .school-book-meta span {
            color: var(--color-text-muted);
            font-size: .52rem;
        }

        .school-book-meta strong {
            margin-top: 2px;
            font-size: .61rem;
        }

        .school-book-section,
        .school-action-card {
            padding: 17px;
        }

        .school-book-section h2,
        .school-action-card h2 {
            margin: 2px 0 13px;
            font-size: .86rem;
        }

        .school-action-card > p {
            color: var(--color-text-muted);
            font-size: .57rem;
            line-height: 1.5;
        }

        .school-action-card .btn {
            margin-top: 7px;
        }

        .btn--block {
            width: 100%;
            justify-content: center;
        }

        .loan-status {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            padding: 9px;
            border-radius: var(--radius-md);
            background: var(--color-surface-soft);
            font-size: .55rem;
        }

        .school-action-card dl {
            margin: 0;
        }

        .school-action-card dl > div {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            padding: 7px 0;
            border-bottom: 1px solid var(--color-border);
        }

        .school-action-card dt {
            color: var(--color-text-muted);
            font-size: .53rem;
        }

        .school-action-card dd {
            margin: 0;
            font-size: .56rem;
            font-weight: 700;
        }

        @media (max-width: 900px) {

            .school-book-layout {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 600px) {

            .school-book-overview {
                grid-template-columns: 1fr;
            }

            .school-book-cover {
                width: 140px;
            }

        }

    </style>

</x-layouts.dashboard>