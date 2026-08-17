<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>
                <span class="eyebrow">
                    Institutional Licence
                </span>

                <h1>
                    {{ $license->book?->title }}
                </h1>

                <p>
                    {{ $license->license_number }}
                </p>
            </div>


            <a
                href="{{ route(
                    'school.library.licenses.index'
                ) }}"
                class="btn btn--secondary"
            >
                Back
            </a>

        </div>


        <div class="card" style="padding:18px;">

            <div style="margin-bottom:18px;">

                <x-library.license-status
                    :status="$license->status"
                />

            </div>


            <x-library.book-rights
                :book="$license->book"
                :license="$license"
            />


            <dl class="licence-details">

                <div>
                    <dt>Licence Type</dt>
                    <dd>
                        {{ str(
                            $license->license_type
                        )->title() }}
                    </dd>
                </div>

                <div>
                    <dt>Starts</dt>
                    <dd>
                        {{ $license->starts_at
                            ?->format('d M Y')
                        }}
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

                <div>
                    <dt>Seat Limit</dt>
                    <dd>
                        {{ $license->seat_limit
                            ?? 'Unlimited'
                        }}
                    </dd>
                </div>

                <div>
                    <dt>Concurrent Readers</dt>
                    <dd>
                        {{ $license->concurrent_reader_limit
                            ?? 'Unlimited'
                        }}
                    </dd>
                </div>

            </dl>

        </div>

    </div>


    <style>

        .licence-details {
            margin-top: 20px;
        }

        .licence-details > div {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 15px;
            padding: 9px 0;
            border-bottom: 1px solid var(--color-border);
        }

        .licence-details dt {
            color: var(--color-text-muted);
            font-size: .56rem;
        }

        .licence-details dd {
            margin: 0;
            font-size: .6rem;
            font-weight: 700;
        }

    </style>

</x-layouts.dashboard>