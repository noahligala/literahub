<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Distribution
                </span>

                <h1>
                    Book Licences
                </h1>

                <p>
                    Manage institutional access rights,
                    licence periods and digital distribution permissions.
                </p>

            </div>


            @can(
                'create',
                App\Models\SchoolBookLicense::class
            )

                <a
                    href="{{ route('book-licenses.create') }}"
                    class="btn btn--primary"
                >
                    Issue Licence
                </a>

            @endcan

        </div>


        <div class="card">

            @if ($licenses->count())

                <div class="table-wrapper">

                    <table class="table-condensed">

                        <thead>

                            <tr>
                                <th>Licence</th>
                                <th>Book</th>
                                <th>School</th>
                                <th>Licensor</th>
                                <th>Type</th>
                                <th>Period</th>
                                <th>Status</th>
                                <th></th>
                            </tr>

                        </thead>


                        <tbody>

                            @foreach ($licenses as $license)

                                <tr>

                                    <td>

                                        <a
                                            href="{{ route(
                                                'book-licenses.show',
                                                $license
                                            ) }}"
                                            class="table-link"
                                        >
                                            {{ $license->license_number }}
                                        </a>

                                    </td>


                                    <td>

                                        <span class="table-value">
                                            {{ $license->book?->title
                                                ?? 'Unknown book'
                                            }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="table-value">
                                            {{ $license->school?->name
                                                ?? 'Unknown school'
                                            }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="table-value">

                                            {{ $license->publisher?->name
                                                ?? $license->author?->name
                                                ?? 'Unknown'
                                            }}

                                        </span>

                                    </td>


                                    <td>

                                        <span class="table-value">
                                            {{ str(
                                                $license->license_type
                                            )->title() }}
                                        </span>

                                    </td>


                                    <td>

                                        <span class="table-value">

                                            {{ $license->starts_at
                                                ?->format('d M Y')
                                            }}

                                        </span>

                                        <span class="table-secondary">

                                            to
                                            {{ $license->expires_at
                                                ?->format('d M Y')
                                                ?? 'No expiry'
                                            }}

                                        </span>

                                    </td>


                                    <td>

                                        <x-library.license-status
                                            :status="$license->status"
                                        />

                                    </td>


                                    <td>

                                        <div class="table-icon-actions">

                                            <a
                                                href="{{ route(
                                                    'book-licenses.show',
                                                    $license
                                                ) }}"
                                                class="table-icon-button"
                                                title="View licence"
                                            >

                                                <svg viewBox="0 0 24 24">
                                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>

                                            </a>


                                            @can('update', $license)

                                                <a
                                                    href="{{ route(
                                                        'book-licenses.edit',
                                                        $license
                                                    ) }}"
                                                    class="table-icon-button"
                                                    title="Edit licence"
                                                >

                                                    <svg viewBox="0 0 24 24">
                                                        <path d="M12 20h9"/>
                                                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                                    </svg>

                                                </a>

                                            @endcan

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                <div class="pagination-shell">
                    {{ $licenses->links() }}
                </div>

            @else

                <div class="empty-state">

                    <h2>
                        No licences yet
                    </h2>

                    <p>
                        No institutional book licences have been created.
                    </p>

                </div>

            @endif

        </div>

    </div>

</x-layouts.dashboard>