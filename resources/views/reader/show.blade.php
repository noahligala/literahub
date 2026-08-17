<x-layouts.dashboard>

    <div
        class="reader-shell"
        data-pdf-reader
        data-pdf-url="{{ route('reader.stream', $book) }}"
        data-initial-page="{{ $bookmark?->page ?? 1 }}"
    >

        {{-- ================================================================
             HEADER
        ================================================================= --}}

        <header class="reader-header">

            <div class="reader-title">

                <a
                    href="{{ $school
                        ? route('school.library.show', $book)
                        : route('books.show', $book)
                    }}"
                    class="reader-back"
                    aria-label="Back"
                >
                    ←
                </a>


                <div>

                    <span>
                        Protected Reader
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

            </div>


            <div class="reader-actions">

                @if ($canDownload)

                    <a
                        href="{{ route(
                            'reader.download',
                            $book
                        ) }}"
                        class="btn btn--secondary"
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
                        class="btn btn--secondary"
                    >
                        Print
                    </a>

                @endif

            </div>

        </header>


        {{-- ================================================================
             TOOLBAR
        ================================================================= --}}

        <div class="reader-toolbar">

            <div class="reader-navigation">

                <button
                    type="button"
                    class="reader-tool"
                    data-page-previous
                    disabled
                >
                    Previous
                </button>


                <div class="reader-page-field">

                    <input
                        type="number"
                        value="{{ $bookmark?->page ?? 1 }}"
                        min="1"
                        data-page-input
                        aria-label="Page number"
                    >

                    <span>
                        /
                        <strong data-page-count>
                            —
                        </strong>
                    </span>

                </div>


                <button
                    type="button"
                    class="reader-tool"
                    data-page-next
                >
                    Next
                </button>

            </div>


            <div class="reader-zoom">

                <button
                    type="button"
                    class="reader-tool reader-tool--square"
                    data-zoom-out
                    aria-label="Zoom out"
                >
                    −
                </button>

                <span data-zoom-label>
                    125%
                </span>

                <button
                    type="button"
                    class="reader-tool reader-tool--square"
                    data-zoom-in
                    aria-label="Zoom in"
                >
                    +
                </button>

            </div>


            @if ($canBookmark)

                <form
                    method="POST"
                    action="{{ route(
                        'school.library.bookmarks.store',
                        $book
                    ) }}"
                    class="reader-bookmark"
                    data-bookmark-form
                >

                    @csrf

                    <input
                        type="hidden"
                        name="page"
                        value="{{ $bookmark?->page ?? 1 }}"
                        data-bookmark-page
                    >

                    <input
                        type="hidden"
                        name="label"
                        value="Reader bookmark"
                    >

                    <button
                        type="submit"
                        class="reader-tool"
                    >
                        Bookmark Page
                    </button>

                </form>

            @endif

        </div>


        {{-- ================================================================
             VIEWER
        ================================================================= --}}

        <main class="reader-workspace">

            <div
                class="reader-loading"
                data-reader-loading
            >

                <div class="reader-spinner"></div>

                <strong>
                    Loading book…
                </strong>

                <span>
                    Preparing the protected reader.
                </span>

            </div>


            <div
                class="reader-error"
                data-reader-error
                hidden
            >

                <strong>
                    Unable to load this book.
                </strong>

                <p>
                    Refresh the page or verify that your
                    access licence is still active.
                </p>

            </div>


            <div
                class="reader-stage"
                data-reader-stage
                hidden
            >

                <canvas
                    data-pdf-canvas
                ></canvas>

            </div>

        </main>


        {{-- ================================================================
             RIGHTS NOTICE
        ================================================================= --}}

        <footer class="reader-footer">

            <span>
                {{ $book->rights_statement
                    ?: 'This work is protected by its applicable copyright and distribution terms.'
                }}
            </span>

        </footer>

    </div>


    <script>
        document.addEventListener(
            'DOMContentLoaded',
            () => {

                const reader =
                    document.querySelector(
                        '[data-pdf-reader]'
                    );

                const bookmarkPage =
                    document.querySelector(
                        '[data-bookmark-page]'
                    );


                if (
                    !reader
                    || !bookmarkPage
                ) {
                    return;
                }


                reader.addEventListener(
                    'literahub:page-changed',
                    event => {

                        bookmarkPage.value =
                            event.detail.page;

                    }
                );

            }
        );
    </script>


    <style>

        .reader-shell {
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 70px);
            margin: -18px;
            background: var(--color-surface-soft);
        }

        .reader-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 12px 18px;
            border-bottom: 1px solid var(--color-border);
            background: var(--color-surface);
        }

        .reader-title {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .reader-back {
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            color: var(--color-text);
            text-decoration: none;
        }

        .reader-title > div {
            min-width: 0;
        }

        .reader-title span {
            color: var(--color-primary);
            font-size: .52rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .reader-title h1 {
            overflow: hidden;
            margin: 1px 0;
            color: var(--color-text);
            font-size: .78rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .reader-title p {
            overflow: hidden;
            margin: 0;
            color: var(--color-text-muted);
            font-size: .53rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .reader-actions {
            display: flex;
            gap: 6px;
        }

        .reader-toolbar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            min-height: 48px;
            padding: 7px 15px;
            border-bottom: 1px solid var(--color-border);
            background: var(--color-surface);
        }

        .reader-navigation,
        .reader-zoom {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .reader-tool {
            min-height: 29px;
            padding: 0 9px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            background: var(--color-surface);
            color: var(--color-text);
            font-size: .56rem;
            font-weight: 700;
            cursor: pointer;
        }

        .reader-tool:hover:not(:disabled) {
            border-color: var(--brand-300);
            background: var(--color-surface-soft);
        }

        .reader-tool:disabled {
            cursor: not-allowed;
            opacity: .45;
        }

        .reader-tool--square {
            width: 29px;
            padding: 0;
            font-size: .8rem;
        }

        .reader-page-field {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--color-text-muted);
            font-size: .56rem;
        }

        .reader-page-field input {
            width: 50px;
            min-height: 29px;
            padding: 3px 5px;
            text-align: center;
        }

        .reader-zoom > span {
            width: 38px;
            color: var(--color-text-muted);
            font-size: .54rem;
            text-align: center;
        }

        .reader-workspace {
            position: relative;
            flex: 1;
            min-height: 650px;
            overflow: auto;
            padding: 28px;
        }

        .reader-stage {
            min-width: max-content;
            text-align: center;
        }

        .reader-stage canvas {
            display: inline-block;
            max-width: none;
            background: white;
            box-shadow:
                0 8px 28px
                rgba(0, 0, 0, .12);
        }

        .reader-loading,
        .reader-error {
            min-height: 420px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .reader-loading strong,
        .reader-error strong {
            margin-top: 10px;
            font-size: .72rem;
        }

        .reader-loading span,
        .reader-error p {
            margin: 4px 0;
            color: var(--color-text-muted);
            font-size: .58rem;
        }

        .reader-spinner {
            width: 26px;
            height: 26px;
            border: 2px solid var(--color-border);
            border-top-color: var(--color-primary);
            border-radius: 50%;
            animation: reader-spin .8s linear infinite;
        }

        .reader-footer {
            padding: 8px 18px;
            border-top: 1px solid var(--color-border);
            background: var(--color-surface);
            color: var(--color-text-muted);
            font-size: .49rem;
            text-align: center;
        }

        @keyframes reader-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 700px) {

            .reader-header {
                align-items: flex-start;
            }

            .reader-actions {
                flex-wrap: wrap;
            }

            .reader-toolbar {
                justify-content: flex-start;
                overflow-x: auto;
            }

            .reader-workspace {
                padding: 14px;
            }

        }

    </style>

</x-layouts.dashboard>