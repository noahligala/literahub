@props([
    'book' => null,
    'publishers' => collect(),
    'authors' => collect(),
])

@php
    $editing = filled($book);

    $selectedAuthors = collect(
        old(
            'author_ids',
            $editing
                ? $book->authors->pluck('id')->all()
                : []
        )
    )
        ->map(fn ($id) => (string) $id)
        ->all();
@endphp


<div class="book-form-layout">

    {{-- ====================================================================
         MAIN
    ===================================================================== --}}

    <div class="book-form-main">


        {{-- ================================================================
             BOOK INFORMATION
        ================================================================= --}}

        <section class="card form-section">

            <div class="form-section__header">

                <div>
                    <span class="eyebrow">
                        Catalogue
                    </span>

                    <h2>
                        Book Information
                    </h2>
                </div>

            </div>


            <div class="form-grid form-grid--2">

                {{-- Title --}}
                <div class="form-group form-group--full">

                    <label for="title">
                        Book Title
                    </label>

                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title', $book?->title) }}"
                        required
                    >

                    @error('title')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- ISBN --}}
                <div class="form-group">

                    <label for="isbn">
                        ISBN
                    </label>

                    <input
                        id="isbn"
                        type="text"
                        name="isbn"
                        value="{{ old('isbn', $book?->isbn) }}"
                        placeholder="9780000000000"
                        required
                    >

                    <small>
                        ISBN-10 or ISBN-13 with a valid checksum.
                    </small>

                    @error('isbn')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Edition --}}
                <div class="form-group">

                    <label for="edition">
                        Edition
                    </label>

                    <input
                        id="edition"
                        type="text"
                        name="edition"
                        value="{{ old('edition', $book?->edition) }}"
                        placeholder="e.g. 2nd Edition"
                    >

                </div>


                {{-- Publication year --}}
                <div class="form-group">

                    <label for="publication_year">
                        Publication Year
                    </label>

                    <input
                        id="publication_year"
                        type="number"
                        name="publication_year"
                        min="1000"
                        max="{{ now()->year }}"
                        value="{{ old('publication_year', $book?->publication_year) }}"
                    >

                </div>


                {{-- Language --}}
                <div class="form-group">

                    <label for="language">
                        Language
                    </label>

                    <input
                        id="language"
                        type="text"
                        name="language"
                        value="{{ old('language', $book?->language ?? 'English') }}"
                        required
                    >

                </div>


                {{-- Category --}}
                <div class="form-group">

                    <label for="category">
                        Category
                    </label>

                    <input
                        id="category"
                        type="text"
                        name="category"
                        value="{{ old('category', $book?->category) }}"
                        placeholder="Novel, Poetry, Drama..."
                    >

                </div>


                {{-- Publisher --}}
                <div class="form-group">

                    <label for="publisher_id">
                        Publisher
                    </label>

                    <select
                        id="publisher_id"
                        name="publisher_id"
                    >

                        <option value="">
                            Independent / No Publisher
                        </option>

                        @foreach ($publishers as $publisher)

                            <option
                                value="{{ $publisher->id }}"
                                @selected(
                                    (string) old(
                                        'publisher_id',
                                        $book?->publisher_id
                                    )
                                    ===
                                    (string) $publisher->id
                                )
                            >
                                {{ $publisher->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Description --}}
                <div class="form-group form-group--full">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        placeholder="Book summary, subject matter and intended audience..."
                    >{{ old('description', $book?->description) }}</textarea>

                </div>

            </div>

        </section>


        {{-- ================================================================
             AUTHORS
        ================================================================= --}}

        <section class="card form-section">

            <div class="form-section__header">

                <div>

                    <span class="eyebrow">
                        Intellectual Property
                    </span>

                    <h2>
                        Authors
                    </h2>

                    <p>
                        Select every author associated with this work.
                    </p>

                </div>

            </div>


            @if ($authors->count())

                <div class="author-selection">

                    @foreach ($authors as $author)

                        <label class="author-option">

                            <input
                                type="checkbox"
                                name="author_ids[]"
                                value="{{ $author->id }}"
                                @checked(
                                    in_array(
                                        (string) $author->id,
                                        $selectedAuthors,
                                        true
                                    )
                                )
                            >

                            <span class="author-option__avatar">

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


                            <span class="author-option__body">

                                <strong>
                                    {{ $author->name }}
                                </strong>

                                <small>
                                    {{ $author->publisher?->name ?? 'Independent author' }}
                                </small>

                            </span>

                        </label>

                    @endforeach

                </div>

            @else

                <div class="empty-inline">

                    No verified authors are currently available.

                    @if (Route::has('authors.create'))

                        <a href="{{ route('authors.create') }}">
                            Add an author
                        </a>

                    @endif

                </div>

            @endif


            @error('author_ids')
                <div class="field-error">
                    {{ $message }}
                </div>
            @enderror

        </section>


        {{-- ================================================================
             FILES
        ================================================================= --}}

        <section class="card form-section">

            <div class="form-section__header">

                <div>

                    <span class="eyebrow">
                        Digital Asset
                    </span>

                    <h2>
                        Files
                    </h2>

                </div>

            </div>


            <div class="form-grid form-grid--2">

                {{-- PDF --}}
                <div class="form-group">

                    <label for="pdf">
                        PDF Book
                    </label>

                    <input
                        id="pdf"
                        type="file"
                        name="pdf"
                        accept="application/pdf"
                        @required(!$editing)
                    >

                    @if ($editing && $book?->pdf_path)

                        <small>
                            A protected PDF is already uploaded.
                            Choose another file only to replace it.
                        </small>

                    @else

                        <small>
                            PDF only. Maximum 100 MB.
                        </small>

                    @endif

                    @error('pdf')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Cover --}}
                <div class="form-group">

                    <label for="cover">
                        Book Cover
                    </label>

                    <input
                        id="cover"
                        type="file"
                        name="cover"
                        accept="image/png,image/jpeg,image/webp"
                    >

                    <small>
                        Optional cover image.
                    </small>

                    @error('cover')
                        <div class="field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </section>


        {{-- ================================================================
             RIGHTS
        ================================================================= --}}

        <section class="card form-section">

            <div class="form-section__header">

                <div>

                    <span class="eyebrow">
                        Distribution
                    </span>

                    <h2>
                        Rights & Permissions
                    </h2>

                    <p>
                        These are the maximum rights the platform may grant.
                        A school licence can restrict them further, but cannot
                        expand them.
                    </p>

                </div>

            </div>


            <div class="rights-grid">


                {{-- Online Reading --}}
                <label class="rights-option">

                    <input
                        type="checkbox"
                        name="allow_online_reading"
                        value="1"
                        @checked(
                            old(
                                'allow_online_reading',
                                $editing
                                    ? $book->allow_online_reading
                                    : true
                            )
                        )
                    >

                    <span>

                        <strong>
                            Online Reading
                        </strong>

                        <small>
                            Allow protected reading inside LiteraHub.
                        </small>

                    </span>

                </label>


                {{-- Borrowing --}}
                <label class="rights-option">

                    <input
                        type="checkbox"
                        name="allow_student_borrowing"
                        value="1"
                        @checked(
                            old(
                                'allow_student_borrowing',
                                $editing
                                    ? $book->allow_student_borrowing
                                    : true
                            )
                        )
                    >

                    <span>

                        <strong>
                            Student Borrowing
                        </strong>

                        <small>
                            Permit time-limited digital borrowing.
                        </small>

                    </span>

                </label>


                {{-- Teacher assignment --}}
                <label class="rights-option">

                    <input
                        type="checkbox"
                        name="allow_teacher_assignment"
                        value="1"
                        @checked(
                            old(
                                'allow_teacher_assignment',
                                $editing
                                    ? $book->allow_teacher_assignment
                                    : true
                            )
                        )
                    >

                    <span>

                        <strong>
                            Teacher Assignment
                        </strong>

                        <small>
                            Teachers may assign this title to classes.
                        </small>

                    </span>

                </label>


                {{-- Print --}}
                <label class="rights-option">

                    <input
                        type="checkbox"
                        name="allow_print"
                        value="1"
                        @checked(
                            old(
                                'allow_print',
                                $editing
                                    ? $book->allow_print
                                    : false
                            )
                        )
                    >

                    <span>

                        <strong>
                            Printing
                        </strong>

                        <small>
                            Permit authorised users to print the PDF.
                        </small>

                    </span>

                </label>


                {{-- Download --}}
                <label class="rights-option">

                    <input
                        type="checkbox"
                        name="allow_download"
                        value="1"
                        @checked(
                            old(
                                'allow_download',
                                $editing
                                    ? $book->allow_download
                                    : false
                            )
                        )
                    >

                    <span>

                        <strong>
                            Download
                        </strong>

                        <small>
                            Permit authorised users to download the file.
                        </small>

                    </span>

                </label>

            </div>


            <div class="form-grid form-grid--2 form-grid--spaced">

                {{-- Loan Days --}}
                <div class="form-group">

                    <label for="loan_days">
                        Loan Period
                    </label>

                    <input
                        id="loan_days"
                        type="number"
                        name="loan_days"
                        min="1"
                        max="365"
                        value="{{ old('loan_days', $book?->loan_days ?? 14) }}"
                    >

                    <small>
                        Number of days a student keeps a borrowed title.
                    </small>

                </div>


                {{-- Concurrent Loans --}}
                <div class="form-group">

                    <label for="max_concurrent_loans">
                        Maximum Concurrent Loans
                    </label>

                    <input
                        id="max_concurrent_loans"
                        type="number"
                        name="max_concurrent_loans"
                        min="1"
                        value="{{ old(
                            'max_concurrent_loans',
                            $book?->max_concurrent_loans
                        ) }}"
                        placeholder="Unlimited"
                    >

                </div>


                {{-- Rights Statement --}}
                <div class="form-group form-group--full">

                    <label for="rights_statement">
                        Rights Statement
                    </label>

                    <textarea
                        id="rights_statement"
                        name="rights_statement"
                        rows="5"
                        placeholder="Copyright and distribution terms..."
                    >{{ old('rights_statement', $book?->rights_statement) }}</textarea>

                </div>

            </div>

        </section>

    </div>


    {{-- ====================================================================
         SIDEBAR
    ===================================================================== --}}

    <aside class="book-form-sidebar">

        <div class="card publish-card">

            <span class="eyebrow">
                Workflow
            </span>

            <h2>
                {{ $editing ? 'Save Changes' : 'Submit Book' }}
            </h2>


            @if ($editing)

                <p>
                    Updating this title sends it back into the
                    content review workflow.
                </p>

                <div class="current-status">

                    <span>
                        Current status
                    </span>

                    <x-library.book-status
                        :status="$book->status"
                    />

                </div>

            @else

                <p>
                    New uploads are placed under review before they can
                    become available to students.
                </p>

            @endif


            <button
                type="submit"
                class="btn btn--primary btn--block"
            >
                {{ $editing ? 'Save & Resubmit' : 'Upload & Submit' }}
            </button>


            <a
                href="{{ $editing
                    ? route('books.show', $book)
                    : route('books.index')
                }}"
                class="btn btn--secondary btn--block"
            >
                Cancel
            </a>

        </div>


        <div class="card rights-note">

            <strong>
                Intellectual Property
            </strong>

            <p>
                Uploading a work does not transfer ownership to LiteraHub.
                Copyright remains with the author or authorised rights holder.
            </p>

        </div>

    </aside>

</div>


<style>
    .book-form-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 280px;
        gap: 16px;
        align-items: start;
    }

    .book-form-main {
        display: flex;
        flex-direction: column;
        gap: 14px;
        min-width: 0;
    }

    .book-form-sidebar {
        display: flex;
        flex-direction: column;
        gap: 12px;
        position: sticky;
        top: 88px;
    }

    .form-section {
        padding: 18px;
    }

    .form-section__header {
        margin-bottom: 16px;
    }

    .form-section__header h2 {
        margin: 2px 0 0;
        color: var(--color-text);
        font-size: .9rem;
    }

    .form-section__header p {
        margin: 4px 0 0;
        max-width: 700px;
        color: var(--color-text-muted);
        font-size: .63rem;
        line-height: 1.5;
    }

    .form-grid {
        display: grid;
        gap: 13px;
    }

    .form-grid--2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .form-grid--spaced {
        margin-top: 18px;
    }

    .form-group--full {
        grid-column: 1 / -1;
    }

    .form-group small {
        display: block;
        margin-top: 4px;
        color: var(--color-text-muted);
        font-size: .56rem;
        line-height: 1.4;
    }

    .author-selection {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .author-option {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 10px;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
        cursor: pointer;
    }

    .author-option:hover {
        background: var(--color-surface-soft);
    }

    .author-option__avatar {
        width: 30px;
        height: 30px;
        flex: 0 0 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: var(--color-surface-soft);
        color: var(--color-primary);
        font-size: .56rem;
        font-weight: 800;
    }

    .author-option__body {
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    .author-option__body strong {
        color: var(--color-text);
        font-size: .66rem;
    }

    .author-option__body small {
        color: var(--color-text-muted);
        font-size: .54rem;
    }

    .rights-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .rights-option {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 11px;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
        cursor: pointer;
    }

    .rights-option:hover {
        background: var(--color-surface-soft);
    }

    .rights-option input {
        width: auto;
        margin-top: 2px;
    }

    .rights-option span {
        display: flex;
        flex-direction: column;
    }

    .rights-option strong {
        color: var(--color-text);
        font-size: .64rem;
    }

    .rights-option small {
        margin-top: 2px;
        color: var(--color-text-muted);
        font-size: .54rem;
        line-height: 1.4;
    }

    .publish-card {
        padding: 17px;
    }

    .publish-card h2 {
        margin: 3px 0 6px;
        color: var(--color-text);
        font-size: .9rem;
    }

    .publish-card p {
        margin: 0 0 14px;
        color: var(--color-text-muted);
        font-size: .61rem;
        line-height: 1.5;
    }

    .publish-card .btn {
        margin-top: 7px;
    }

    .btn--block {
        width: 100%;
        justify-content: center;
    }

    .current-status {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin: 12px 0;
        padding: 9px 0;
        border-top: 1px solid var(--color-border);
        border-bottom: 1px solid var(--color-border);
    }

    .current-status > span {
        color: var(--color-text-muted);
        font-size: .58rem;
    }

    .rights-note {
        padding: 15px;
    }

    .rights-note strong {
        color: var(--color-text);
        font-size: .65rem;
    }

    .rights-note p {
        margin: 5px 0 0;
        color: var(--color-text-muted);
        font-size: .57rem;
        line-height: 1.5;
    }

    .empty-inline {
        padding: 14px;
        border: 1px dashed var(--color-border);
        border-radius: var(--radius-md);
        color: var(--color-text-muted);
        font-size: .62rem;
    }

    @media (max-width: 900px) {
        .book-form-layout {
            grid-template-columns: 1fr;
        }

        .book-form-sidebar {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .form-grid--2,
        .author-selection,
        .rights-grid {
            grid-template-columns: 1fr;
        }
    }
</style>