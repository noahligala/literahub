@props([
    'license' => null,
    'schools' => collect(),
    'books' => collect(),
    'publishers' => collect(),
    'authors' => collect(),
])

@php
    $editing = filled($license);
@endphp


<div class="license-form-layout">

    <div class="license-form-main">


        {{-- ================================================================
             CORE LICENCE
        ================================================================= --}}

        <section class="card form-section">

            <div class="form-section__header">

                <span class="eyebrow">
                    Licence
                </span>

                <h2>
                    Institutional Entitlement
                </h2>

            </div>


            <div class="form-grid form-grid--2">


                {{-- School --}}
                <div class="form-group">

                    <label for="school_id">
                        School
                    </label>

                    <select
                        id="school_id"
                        name="school_id"
                        required
                    >

                        <option value="">
                            Select school
                        </option>

                        @foreach ($schools as $school)

                            <option
                                value="{{ $school->id }}"
                                @selected(
                                    (string) old(
                                        'school_id',
                                        $license?->school_id
                                    )
                                    ===
                                    (string) $school->id
                                )
                            >
                                {{ $school->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Book --}}
                <div class="form-group">

                    <label for="book_id">
                        Book
                    </label>

                    <select
                        id="book_id"
                        name="book_id"
                        required
                    >

                        <option value="">
                            Select book
                        </option>

                        @foreach ($books as $book)

                            <option
                                value="{{ $book->id }}"
                                @selected(
                                    (string) old(
                                        'book_id',
                                        $license?->book_id
                                        ?? request('book')
                                    )
                                    ===
                                    (string) $book->id
                                )
                            >
                                {{ $book->title }}
                                — {{ $book->isbn }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Licence type --}}
                <div class="form-group">

                    <label for="license_type">
                        Licence Type
                    </label>

                    <select
                        id="license_type"
                        name="license_type"
                        required
                    >

                        @foreach ([
                            'lease' => 'Lease',
                            'subscription' => 'Subscription',
                            'perpetual' => 'Perpetual',
                            'trial' => 'Trial',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    old(
                                        'license_type',
                                        $license?->license_type
                                        ?? 'lease'
                                    )
                                    === $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Status --}}
                <div class="form-group">

                    <label for="status">
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        required
                    >

                        @foreach ([
                            'pending' => 'Pending',
                            'active' => 'Active',
                            'suspended' => 'Suspended',
                            'expired' => 'Expired',
                            'revoked' => 'Revoked',
                        ] as $value => $label)

                            <option
                                value="{{ $value }}"
                                @selected(
                                    old(
                                        'status',
                                        $license?->status
                                        ?? 'active'
                                    )
                                    === $value
                                )
                            >
                                {{ $label }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

        </section>


        {{-- ================================================================
             LICENSOR
        ================================================================= --}}

        <section class="card form-section">

            <div class="form-section__header">

                <span class="eyebrow">
                    Rights Holder
                </span>

                <h2>
                    Licensor
                </h2>

                <p>
                    Choose either the publisher or author who is granting
                    this licence.
                </p>

            </div>


            <div class="form-grid form-grid--2">

                <div class="form-group">

                    <label for="publisher_id">
                        Publisher
                    </label>

                    <select
                        id="publisher_id"
                        name="publisher_id"
                    >

                        <option value="">
                            No publisher
                        </option>

                        @foreach ($publishers as $publisher)

                            <option
                                value="{{ $publisher->id }}"
                                @selected(
                                    (string) old(
                                        'publisher_id',
                                        $license?->publisher_id
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


                <div class="form-group">

                    <label for="author_id">
                        Author
                    </label>

                    <select
                        id="author_id"
                        name="author_id"
                    >

                        <option value="">
                            No author
                        </option>

                        @foreach ($authors as $author)

                            <option
                                value="{{ $author->id }}"
                                @selected(
                                    (string) old(
                                        'author_id',
                                        $license?->author_id
                                    )
                                    ===
                                    (string) $author->id
                                )
                            >
                                {{ $author->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

        </section>


        {{-- ================================================================
             PERIOD & LIMITS
        ================================================================= --}}

        <section class="card form-section">

            <div class="form-section__header">

                <span class="eyebrow">
                    Availability
                </span>

                <h2>
                    Period & Limits
                </h2>

            </div>


            <div class="form-grid form-grid--2">

                <div class="form-group">

                    <label for="starts_at">
                        Starts At
                    </label>

                    <input
                        id="starts_at"
                        type="datetime-local"
                        name="starts_at"
                        value="{{ old(
                            'starts_at',
                            $license?->starts_at
                                ?->format('Y-m-d\TH:i')
                            ?? now()->format('Y-m-d\TH:i')
                        ) }}"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="expires_at">
                        Expires At
                    </label>

                    <input
                        id="expires_at"
                        type="datetime-local"
                        name="expires_at"
                        value="{{ old(
                            'expires_at',
                            $license?->expires_at
                                ?->format('Y-m-d\TH:i')
                        ) }}"
                    >

                </div>


                <div class="form-group">

                    <label for="seat_limit">
                        Seat Limit
                    </label>

                    <input
                        id="seat_limit"
                        type="number"
                        name="seat_limit"
                        min="1"
                        value="{{ old(
                            'seat_limit',
                            $license?->seat_limit
                        ) }}"
                        placeholder="Unlimited"
                    >

                </div>


                <div class="form-group">

                    <label for="concurrent_reader_limit">
                        Concurrent Readers
                    </label>

                    <input
                        id="concurrent_reader_limit"
                        type="number"
                        name="concurrent_reader_limit"
                        min="1"
                        value="{{ old(
                            'concurrent_reader_limit',
                            $license?->concurrent_reader_limit
                        ) }}"
                        placeholder="Unlimited"
                    >

                </div>

            </div>

        </section>


        {{-- ================================================================
             RIGHTS
        ================================================================= --}}

        <section class="card form-section">

            <div class="form-section__header">

                <span class="eyebrow">
                    Permissions
                </span>

                <h2>
                    School Rights
                </h2>

                <p>
                    These permissions can restrict the underlying book rights
                    but cannot expand them.
                </p>

            </div>


            <div class="rights-grid">

                @foreach ([
                    'allow_student_reading'
                        => 'Student Reading',

                    'allow_teacher_reading'
                        => 'Teacher Reading',

                    'allow_teacher_assignment'
                        => 'Teacher Assignment',

                    'allow_student_borrowing'
                        => 'Student Borrowing',

                    'allow_print'
                        => 'Printing',

                    'allow_download'
                        => 'Download',
                ] as $field => $label)

                    @php
                        $defaults = [
                            'allow_student_reading' => true,
                            'allow_teacher_reading' => true,
                            'allow_teacher_assignment' => true,
                            'allow_student_borrowing' => true,
                            'allow_print' => false,
                            'allow_download' => false,
                        ];

                        $checked = old(
                            $field,
                            $editing
                                ? (bool) $license->{$field}
                                : $defaults[$field]
                        );
                    @endphp


                    <label class="rights-option">

                        <input
                            type="hidden"
                            name="{{ $field }}"
                            value="0"
                        >

                        <input
                            type="checkbox"
                            name="{{ $field }}"
                            value="1"
                            @checked($checked)
                        >


                        <span>

                            <strong>
                                {{ $label }}
                            </strong>

                        </span>

                    </label>

                @endforeach

            </div>

        </section>


        {{-- ================================================================
             COMMERCIAL
        ================================================================= --}}

        <section class="card form-section">

            <div class="form-section__header">

                <span class="eyebrow">
                    Commercial Terms
                </span>

                <h2>
                    Pricing & Terms
                </h2>

            </div>


            <div class="form-grid form-grid--2">

                <div class="form-group">

                    <label for="price_minor">
                        Price
                    </label>

                    <input
                        id="price_minor"
                        type="number"
                        name="price_minor"
                        min="0"
                        value="{{ old(
                            'price_minor',
                            $license?->price_minor
                        ) }}"
                    >

                    <small>
                        Stored in the smallest currency unit.
                        For KES, 500000 represents KES 5,000.00
                        if using cents.
                    </small>

                </div>


                <div class="form-group">

                    <label for="currency">
                        Currency
                    </label>

                    <input
                        id="currency"
                        type="text"
                        name="currency"
                        maxlength="3"
                        value="{{ old(
                            'currency',
                            $license?->currency
                            ?? 'KES'
                        ) }}"
                        required
                    >

                </div>


                <div class="form-group form-group--full">

                    <label for="terms">
                        Licence Terms
                    </label>

                    <textarea
                        id="terms"
                        name="terms"
                        rows="5"
                    >{{ old(
                        'terms',
                        $license?->terms
                    ) }}</textarea>

                </div>


                <div class="form-group form-group--full">

                    <label for="notes">
                        Internal Notes
                    </label>

                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                    >{{ old(
                        'notes',
                        $license?->notes
                    ) }}</textarea>

                </div>

            </div>

        </section>

    </div>


    {{-- ====================================================================
         SIDEBAR
    ===================================================================== --}}

    <aside class="license-sidebar">

        <section class="card licence-submit-card">

            <span class="eyebrow">
                Distribution
            </span>

            <h2>
                {{ $editing
                    ? 'Update Licence'
                    : 'Issue Licence'
                }}
            </h2>

            <p>
                Confirm the institution, book, rights holder
                and usage permissions before saving.
            </p>


            <button
                type="submit"
                class="btn btn--primary btn--block"
            >
                {{ $editing
                    ? 'Save Changes'
                    : 'Create Licence'
                }}
            </button>


            <a
                href="{{ $editing
                    ? route(
                        'book-licenses.show',
                        $license
                    )
                    : route(
                        'book-licenses.index'
                    )
                }}"
                class="btn btn--secondary btn--block"
            >
                Cancel
            </a>

        </section>

    </aside>

</div>


<style>

    .license-form-layout {
        display: grid;
        grid-template-columns:
            minmax(0, 1fr)
            280px;
        gap: 16px;
        align-items: start;
    }

    .license-form-main {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .license-sidebar {
        position: sticky;
        top: 88px;
    }

    .form-section {
        padding: 18px;
    }

    .form-section__header {
        margin-bottom: 15px;
    }

    .form-section__header h2 {
        margin: 2px 0 0;
        font-size: .87rem;
    }

    .form-section__header p {
        margin: 4px 0 0;
        color: var(--color-text-muted);
        font-size: .59rem;
    }

    .form-grid {
        display: grid;
        gap: 12px;
    }

    .form-grid--2 {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

    .form-group--full {
        grid-column: 1 / -1;
    }

    .rights-grid {
        display: grid;
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .rights-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
    }

    .rights-option input {
        width: auto;
    }

    .rights-option strong {
        font-size: .62rem;
    }

    .licence-submit-card {
        padding: 17px;
    }

    .licence-submit-card h2 {
        margin: 3px 0 5px;
        font-size: .9rem;
    }

    .licence-submit-card p {
        margin: 0 0 13px;
        color: var(--color-text-muted);
        font-size: .58rem;
        line-height: 1.5;
    }

    .licence-submit-card .btn {
        margin-top: 7px;
    }

    .btn--block {
        width: 100%;
        justify-content: center;
    }

    @media (max-width: 900px) {

        .license-form-layout {
            grid-template-columns: 1fr;
        }

        .license-sidebar {
            position: static;
        }

    }

    @media (max-width: 600px) {

        .form-grid--2,
        .rights-grid {
            grid-template-columns: 1fr;
        }

    }

</style>