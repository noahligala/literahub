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

            <div class="reader-toolbar__left">

                <button
                    type="button"
                    class="reader-tool reader-tool--contents"
                    data-toc-toggle
                    aria-expanded="false"
                    aria-controls="reader-contents"
                >
                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path d="M4 6h16"/>
                        <path d="M4 12h16"/>
                        <path d="M4 18h16"/>
                    </svg>

                    Contents
                </button>

            </div>


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
             READER CONTENT
        ================================================================= --}}

        <div class="reader-content">


            {{-- ============================================================
                 TABLE OF CONTENTS
            ============================================================= --}}

            <aside
                id="reader-contents"
                class="reader-toc"
                data-reader-toc
                hidden
            >

                <div class="reader-toc__header">

                    <div>

                        <span class="eyebrow">
                            Navigation
                        </span>

                        <h2>
                            Contents
                        </h2>

                    </div>


                    <button
                        type="button"
                        class="reader-toc__close"
                        data-toc-close
                        aria-label="Close contents"
                    >
                        ×
                    </button>

                </div>


                <div class="reader-toc__body">

                    <div
                        class="reader-toc__loading"
                        data-toc-loading
                    >

                        <div class="reader-toc__spinner"></div>

                        <span>
                            Loading contents…
                        </span>

                    </div>


                    <div
                        data-toc-body
                    ></div>

                </div>

            </aside>


            {{-- ============================================================
                 VIEWER
            ============================================================= --}}

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

        </div>


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


    {{-- ====================================================================
         BOOKMARK PAGE SYNCHRONISATION
    ===================================================================== --}}

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


                if (!reader) {
                    return;
                }


                if (bookmarkPage) {

                    reader.addEventListener(
                        'literahub:page-changed',
                        event => {

                            bookmarkPage.value =
                                event.detail.page;

                        }
                    );

                }

            }
        );

    </script>


    <style>

        /*
        |--------------------------------------------------------------------------
        | Reader Shell
        |--------------------------------------------------------------------------
        */

        .reader-shell {
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 70px);
            margin: -18px;
            background: var(--color-surface-soft);
        }


        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .reader-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;

            padding:
                12px
                18px;

            border-bottom:
                1px solid
                var(--color-border);

            background:
                var(--color-surface);
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

            flex:
                0 0
                32px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            border:
                1px solid
                var(--color-border);

            border-radius:
                var(--radius-md);

            color:
                var(--color-text);

            text-decoration:
                none;
        }


        .reader-back:hover {
            border-color:
                var(--brand-300);

            background:
                var(--color-surface-soft);
        }


        .reader-title > div {
            min-width: 0;
        }


        .reader-title span {
            color:
                var(--color-primary);

            font-size:
                .52rem;

            font-weight:
                800;

            text-transform:
                uppercase;

            letter-spacing:
                .08em;
        }


        .reader-title h1 {
            overflow:
                hidden;

            margin:
                1px 0;

            color:
                var(--color-text);

            font-size:
                .78rem;

            text-overflow:
                ellipsis;

            white-space:
                nowrap;
        }


        .reader-title p {
            overflow:
                hidden;

            margin:
                0;

            color:
                var(--color-text-muted);

            font-size:
                .53rem;

            text-overflow:
                ellipsis;

            white-space:
                nowrap;
        }


        .reader-actions {
            display:
                flex;

            gap:
                6px;
        }


        /*
        |--------------------------------------------------------------------------
        | Toolbar
        |--------------------------------------------------------------------------
        */

        .reader-toolbar {
            position:
                relative;

            z-index:
                8;

            display:
                grid;

            grid-template-columns:
                1fr
                auto
                1fr;

            align-items:
                center;

            gap:
                16px;

            min-height:
                48px;

            padding:
                7px
                15px;

            border-bottom:
                1px solid
                var(--color-border);

            background:
                var(--color-surface);
        }


        .reader-toolbar__left {
            display:
                flex;

            justify-content:
                flex-start;
        }


        .reader-navigation {
            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                6px;
        }


        .reader-zoom {
            display:
                flex;

            align-items:
                center;

            justify-content:
                flex-end;

            gap:
                6px;
        }


        .reader-bookmark {
            display:
                flex;

            justify-content:
                flex-end;
        }


        .reader-tool {
            min-height:
                29px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                5px;

            padding:
                0 9px;

            border:
                1px solid
                var(--color-border);

            border-radius:
                var(--radius-sm);

            background:
                var(--color-surface);

            color:
                var(--color-text);

            font-size:
                .56rem;

            font-weight:
                700;

            cursor:
                pointer;
        }


        .reader-tool:hover:not(:disabled) {
            border-color:
                var(--brand-300);

            background:
                var(--color-surface-soft);
        }


        .reader-tool:disabled {
            cursor:
                not-allowed;

            opacity:
                .45;
        }


        .reader-tool--square {
            width:
                29px;

            padding:
                0;

            font-size:
                .8rem;
        }


        .reader-tool--contents svg {
            width:
                13px;

            height:
                13px;

            fill:
                none;

            stroke:
                currentColor;

            stroke-width:
                1.8;

            stroke-linecap:
                round;
        }


        .reader-page-field {
            display:
                flex;

            align-items:
                center;

            gap:
                5px;

            color:
                var(--color-text-muted);

            font-size:
                .56rem;
        }


        .reader-page-field input {
            width:
                50px;

            min-height:
                29px;

            padding:
                3px 5px;

            text-align:
                center;
        }


        .reader-zoom > span {
            width:
                38px;

            color:
                var(--color-text-muted);

            font-size:
                .54rem;

            text-align:
                center;
        }


        /*
        |--------------------------------------------------------------------------
        | Reader Body
        |--------------------------------------------------------------------------
        */

        .reader-content {
            position:
                relative;

            display:
                flex;

            flex:
                1;

            min-height:
                0;
        }


        /*
        |--------------------------------------------------------------------------
        | Contents Sidebar
        |--------------------------------------------------------------------------
        */

        .reader-toc {
            width:
                290px;

            flex:
                0 0
                290px;

            max-height:
                calc(100vh - 170px);

            overflow-y:
                auto;

            border-right:
                1px solid
                var(--color-border);

            background:
                var(--color-surface);
        }


        .reader-toc__header {
            position:
                sticky;

            top:
                0;

            z-index:
                3;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                10px;

            padding:
                13px 14px;

            border-bottom:
                1px solid
                var(--color-border);

            background:
                var(--color-surface);
        }


        .reader-toc__header h2 {
            margin:
                2px 0 0;

            color:
                var(--color-text);

            font-size:
                .78rem;
        }


        .reader-toc__close {
            width:
                28px;

            height:
                28px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            border:
                1px solid
                var(--color-border);

            border-radius:
                var(--radius-sm);

            background:
                var(--color-surface);

            color:
                var(--color-text-muted);

            font-size:
                .9rem;

            cursor:
                pointer;
        }


        .reader-toc__close:hover {
            background:
                var(--color-surface-soft);

            color:
                var(--color-text);
        }


        .reader-toc__body {
            padding:
                8px;
        }


        .reader-toc__loading {
            min-height:
                120px;

            display:
                flex;

            flex-direction:
                column;

            align-items:
                center;

            justify-content:
                center;

            gap:
                8px;

            color:
                var(--color-text-muted);

            font-size:
                .54rem;
        }


        .reader-toc__spinner {
            width:
                18px;

            height:
                18px;

            border:
                2px solid
                var(--color-border);

            border-top-color:
                var(--color-primary);

            border-radius:
                50%;

            animation:
                reader-spin
                .8s
                linear
                infinite;
        }


        .reader-toc__empty {
            padding:
                18px 10px;

            color:
                var(--color-text-muted);

            font-size:
                .55rem;

            line-height:
                1.6;

            text-align:
                center;
        }


        /*
        |--------------------------------------------------------------------------
        | Generated TOC Tree
        |--------------------------------------------------------------------------
        |
        | These elements are generated by reader.js.
        |
        */

        .reader-toc-list {
            margin:
                0;

            padding:
                0;

            list-style:
                none;
        }


        .reader-toc-list
        .reader-toc-list {
            margin-left:
                12px;

            padding-left:
                8px;

            border-left:
                1px solid
                var(--color-border);
        }


        .reader-toc-item {
            margin:
                2px 0;
        }


        .reader-toc-row {
            display:
                flex;

            align-items:
                center;

            gap:
                3px;
        }


        .reader-toc-expander {
            width:
                22px;

            height:
                22px;

            flex:
                0 0
                22px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                0;

            border:
                0;

            background:
                transparent;

            color:
                var(--color-text-muted);

            font-size:
                .8rem;

            cursor:
                pointer;

            transition:
                transform
                .15s
                ease;
        }


        .reader-toc-expander.is-open {
            transform:
                rotate(
                    90deg
                );
        }


        .reader-toc-link {
            min-width:
                0;

            width:
                100%;

            min-height:
                30px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                8px;

            padding:
                6px 7px;

            border:
                0;

            border-radius:
                var(--radius-sm);

            background:
                transparent;

            color:
                var(--color-text);

            font:
                inherit;

            font-size:
                .54rem;

            line-height:
                1.35;

            text-align:
                left;

            cursor:
                pointer;
        }


        .reader-toc-link > span:first-child {
            overflow:
                hidden;

            text-overflow:
                ellipsis;
        }


        .reader-toc-link:hover:not(:disabled) {
            background:
                var(--color-surface-soft);
        }


        .reader-toc-link.is-active {
            background:
                var(--color-surface-soft);

            color:
                var(--color-primary);

            font-weight:
                800;
        }


        .reader-toc-link:disabled {
            cursor:
                default;

            opacity:
                .65;
        }


        .reader-toc-page {
            flex:
                0 0
                auto;

            color:
                var(--color-text-muted);

            font-size:
                .47rem;

            font-weight:
                600;
        }


        .reader-toc-children[hidden] {
            display:
                none;
        }


        /*
        |--------------------------------------------------------------------------
        | Viewer
        |--------------------------------------------------------------------------
        */

        .reader-workspace {
            position:
                relative;

            min-width:
                0;

            flex:
                1;

            min-height:
                650px;

            overflow:
                auto;

            padding:
                28px;
        }


        .reader-stage {
            min-width:
                max-content;

            text-align:
                center;
        }


        .reader-stage canvas {
            display:
                inline-block;

            max-width:
                none;

            background:
                white;

            box-shadow:
                0 8px 28px
                rgba(
                    0,
                    0,
                    0,
                    .12
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Loading / Error
        |--------------------------------------------------------------------------
        */

        .reader-loading,
        .reader-error {
            min-height:
                420px;

            display:
                flex;

            flex-direction:
                column;

            align-items:
                center;

            justify-content:
                center;

            text-align:
                center;
        }


        .reader-loading strong,
        .reader-error strong {
            margin-top:
                10px;

            font-size:
                .72rem;
        }


        .reader-loading span,
        .reader-error p {
            margin:
                4px 0;

            color:
                var(--color-text-muted);

            font-size:
                .58rem;
        }


        .reader-spinner {
            width:
                26px;

            height:
                26px;

            border:
                2px solid
                var(--color-border);

            border-top-color:
                var(--color-primary);

            border-radius:
                50%;

            animation:
                reader-spin
                .8s
                linear
                infinite;
        }


        /*
        |--------------------------------------------------------------------------
        | Footer
        |--------------------------------------------------------------------------
        */

        .reader-footer {
            padding:
                8px 18px;

            border-top:
                1px solid
                var(--color-border);

            background:
                var(--color-surface);

            color:
                var(--color-text-muted);

            font-size:
                .49rem;

            text-align:
                center;
        }


        @keyframes reader-spin {

            to {
                transform:
                    rotate(
                        360deg
                    );
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Tablet / Mobile
        |--------------------------------------------------------------------------
        */

        @media (
            max-width: 800px
        ) {

            .reader-toc {
                position:
                    absolute;

                inset:
                    0 auto 0 0;

                z-index:
                    30;

                width:
                    min(
                        320px,
                        88vw
                    );

                max-height:
                    100%;

                box-shadow:
                    14px 0 35px
                    rgba(
                        0,
                        0,
                        0,
                        .18
                    );
            }

        }


        @media (
            max-width: 700px
        ) {

            .reader-header {
                align-items:
                    flex-start;
            }


            .reader-actions {
                flex-wrap:
                    wrap;
            }


            .reader-toolbar {
                display:
                    flex;

                justify-content:
                    flex-start;

                overflow-x:
                    auto;

                scrollbar-width:
                    thin;
            }


            .reader-toolbar__left,
            .reader-navigation,
            .reader-zoom,
            .reader-bookmark {
                flex:
                    0 0
                    auto;
            }


            .reader-workspace {
                padding:
                    14px;
            }

        }

    </style>

</x-layouts.dashboard>