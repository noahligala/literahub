<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Institutional Licence
                </span>

                <h1>
                    {{ $license->license_number }}
                </h1>

                <p>
                    {{ $license->book?->title }}
                    ·
                    {{ $license->school?->name }}
                </p>

            </div>


            <div class="page-header__actions">

                <a
                    href="{{ route('book-licenses.index') }}"
                    class="btn btn--secondary"
                >
                    Back
                </a>


                @can('update', $license)

                    <a
                        href="{{ route(
                            'book-licenses.edit',
                            $license
                        ) }}"
                        class="btn btn--primary"
                    >
                        Edit Licence
                    </a>

                @endcan

            </div>

        </div>


        <div class="licence-detail-layout">


            <div class="licence-detail-main">

                {{-- ========================================================
                     SUMMARY
                ========================================================= --}}

                <section class="card licence-detail-section">

                    <div class="licence-detail-header">

                        <div>

                            <span class="eyebrow">
                                Licence
                            </span>

                            <h2>
                                Overview
                            </h2>

                        </div>


                        <x-library.license-status
                            :status="$license->status"
                        />

                    </div>


                    <div class="licence-meta-grid">

                        <div>
                            <span>School</span>
                            <strong>
                                {{ $license->school?->name }}
                            </strong>
                        </div>

                        <div>
                            <span>Book</span>
                            <strong>
                                {{ $license->book?->title }}
                            </strong>
                        </div>

                        <div>
                            <span>Type</span>
                            <strong>
                                {{ str(
                                    $license->license_type
                                )->title() }}
                            </strong>
                        </div>

                        <div>
                            <span>Licensor</span>
                            <strong>

                                {{ $license->publisher?->name
                                    ?? $license->author?->name
                                    ?? '—'
                                }}

                            </strong>
                        </div>

                        <div>
                            <span>Starts</span>
                            <strong>
                                {{ $license->starts_at
                                    ?->format('d M Y H:i')
                                }}
                            </strong>
                        </div>

                        <div>
                            <span>Expires</span>
                            <strong>
                                {{ $license->expires_at
                                    ?->format('d M Y H:i')
                                    ?? 'No expiry'
                                }}
                            </strong>
                        </div>

                    </div>

                </section>


                {{-- ========================================================
                     RIGHTS
                ========================================================= --}}

                <section class="card licence-detail-section">

                    <div class="licence-detail-header">

                        <div>

                            <span class="eyebrow">
                                Permissions
                            </span>

                            <h2>
                                Effective Rights
                            </h2>

                        </div>

                    </div>


                    <x-library.book-rights
                        :book="$license->book"
                        :license="$license"
                    />

                </section>


                {{-- ========================================================
                     LIMITS
                ========================================================= --}}

                <section class="card licence-detail-section">

                    <div class="licence-detail-header">

                        <div>

                            <span class="eyebrow">
                                Usage
                            </span>

                            <h2>
                                Limits
                            </h2>

                        </div>

                    </div>


                    <div class="licence-meta-grid">

                        <div>
                            <span>Seat Limit</span>
                            <strong>
                                {{ $license->seat_limit
                                    ?? 'Unlimited'
                                }}
                            </strong>
                        </div>

                        <div>
                            <span>Concurrent Readers</span>
                            <strong>
                                {{ $license->concurrent_reader_limit
                                    ?? 'Unlimited'
                                }}
                            </strong>
                        </div>

                    </div>

                </section>


                {{-- ========================================================
                     COMMERCIAL TERMS
                ========================================================= --}}

                <section class="card licence-detail-section">

                    <div class="licence-detail-header">

                        <div>

                            <span class="eyebrow">
                                Commercial
                            </span>

                            <h2>
                                Terms
                            </h2>

                        </div>

                    </div>


                    <div class="licence-meta-grid">

                        <div>
                            <span>Currency</span>
                            <strong>
                                {{ $license->currency }}
                            </strong>
                        </div>

                        <div>
                            <span>Price</span>
                            <strong>

                                @if (
                                    $license->price_minor
                                    !== null
                                )

                                    {{ $license->currency }}
                                    {{ number_format(
                                        $license->price_minor
                                        / 100,
                                        2
                                    ) }}

                                @else
                                    —
                                @endif

                            </strong>
                        </div>

                    </div>


                    @if ($license->terms)

                        <div class="licence-text">

                            <strong>
                                Licence Terms
                            </strong>

                            <p>
                                {{ $license->terms }}
                            </p>

                        </div>

                    @endif


                    @if ($license->notes)

                        <div class="licence-text">

                            <strong>
                                Internal Notes
                            </strong>

                            <p>
                                {{ $license->notes }}
                            </p>

                        </div>

                    @endif

                </section>

            </div>


            {{-- ============================================================
                 SIDEBAR
            ============================================================= --}}

            <aside class="licence-detail-sidebar">

                <section class="card licence-action-card">

                    <span class="eyebrow">
                        Status
                    </span>

                    <h2>
                        Licence Actions
                    </h2>


                    <div class="licence-status-large">

                        <x-library.license-status
                            :status="$license->status"
                        />

                    </div>


                    @can('renew', $license)

                        <form
                            method="POST"
                            action="{{ route(
                                'book-licenses.renew',
                                $license
                            ) }}"
                            class="renew-form"
                        >

                            @csrf


                            <div class="form-group">

                                <label for="renew_starts_at">
                                    Renewal Starts
                                </label>

                                <input
                                    id="renew_starts_at"
                                    type="datetime-local"
                                    name="starts_at"
                                    value="{{ now()
                                        ->format(
                                            'Y-m-d\TH:i'
                                        )
                                    }}"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label for="renew_expires_at">
                                    Renewal Expires
                                </label>

                                <input
                                    id="renew_expires_at"
                                    type="datetime-local"
                                    name="expires_at"
                                >

                            </div>


                            <button
                                type="submit"
                                class="btn btn--secondary btn--block"
                            >
                                Renew Licence
                            </button>

                        </form>

                    @endcan


                    @can('revoke', $license)

                        @if (
                            !in_array(
                                $license->status,
                                [
                                    'revoked',
                                    'expired',
                                ],
                                true
                            )
                        )

                            <form
                                method="POST"
                                action="{{ route(
                                    'book-licenses.revoke',
                                    $license
                                ) }}"
                                onsubmit="
                                    return confirm(
                                        'Revoke this licence?'
                                    );
                                "
                            >

                                @csrf
                                @method('PATCH')


                                <button
                                    type="submit"
                                    class="btn btn--danger btn--block"
                                >
                                    Revoke Licence
                                </button>

                            </form>

                        @endif

                    @endcan

                </section>


                <section class="card licence-action-card">

                    <span class="eyebrow">
                        Audit
                    </span>

                    <h2>
                        Record
                    </h2>


                    <dl>

                        <div>
                            <dt>Created By</dt>
                            <dd>
                                {{ $license->creator?->name
                                    ?? '—'
                                }}
                            </dd>
                        </div>

                        <div>
                            <dt>Created</dt>
                            <dd>
                                {{ $license->created_at
                                    ?->format('d M Y')
                                }}
                            </dd>
                        </div>

                        @if ($license->revoked_at)

                            <div>
                                <dt>Revoked</dt>
                                <dd>
                                    {{ $license->revoked_at
                                        ->format(
                                            'd M Y'
                                        )
                                    }}
                                </dd>
                            </div>

                        @endif

                    </dl>

                </section>

            </aside>

        </div>

    </div>


    <style>

        .licence-detail-layout {
            display: grid;
            grid-template-columns:
                minmax(0, 1fr)
                280px;
            gap: 16px;
            align-items: start;
        }

        .licence-detail-main {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .licence-detail-sidebar {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .licence-detail-section,
        .licence-action-card {
            padding: 17px;
        }

        .licence-detail-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 14px;
        }

        .licence-detail-header h2,
        .licence-action-card h2 {
            margin: 2px 0 0;
            font-size: .87rem;
        }

        .licence-meta-grid {
            display: grid;
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .licence-meta-grid div {
            display: flex;
            flex-direction: column;
        }

        .licence-meta-grid span {
            color: var(--color-text-muted);
            font-size: .53rem;
        }

        .licence-meta-grid strong {
            margin-top: 2px;
            font-size: .62rem;
        }

        .licence-text {
            margin-top: 14px;
            padding: 11px;
            background: var(--color-surface-soft);
            border-radius: var(--radius-md);
        }

        .licence-text strong {
            font-size: .61rem;
        }

        .licence-text p {
            margin: 4px 0 0;
            color: var(--color-text-muted);
            font-size: .58rem;
            line-height: 1.55;
        }

        .licence-status-large {
            margin: 10px 0 14px;
        }

        .licence-action-card .btn {
            margin-top: 8px;
        }

        .renew-form {
            margin-bottom: 12px;
        }

        .btn--block {
            width: 100%;
            justify-content: center;
        }

        .licence-action-card dl {
            margin: 10px 0 0;
        }

        .licence-action-card dl > div {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            padding: 7px 0;
            border-bottom: 1px solid var(--color-border);
        }

        .licence-action-card dt {
            color: var(--color-text-muted);
            font-size: .55rem;
        }

        .licence-action-card dd {
            margin: 0;
            font-size: .57rem;
            font-weight: 700;
        }

        @media (max-width: 900px) {

            .licence-detail-layout {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 600px) {

            .licence-meta-grid {
                grid-template-columns: 1fr 1fr;
            }

        }

        @media (max-width: 430px) {

            .licence-meta-grid {
                grid-template-columns: 1fr;
            }

        }

    </style>

</x-layouts.dashboard>