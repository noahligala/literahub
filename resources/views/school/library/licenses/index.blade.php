<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>
                <span class="eyebrow">
                    Institutional Licensing
                </span>

                <h1>
                    Book Licences
                </h1>

                <p>
                    Review the titles currently licensed
                    to your institution.
                </p>
            </div>


            <a
                href="{{ route(
                    'school.library.licenses.catalogue'
                ) }}"
                class="btn btn--primary"
            >
                Browse Licence Catalogue
            </a>

        </div>


        <div class="card">

            @if ($licenses->count())

                <div class="table-wrapper">

                    <table class="table-condensed">

                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Licence</th>
                                <th>Type</th>
                                <th>Starts</th>
                                <th>Expires</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>


                        <tbody>

                            @foreach ($licenses as $license)

                                <tr>

                                    <td>
                                        {{ $license->book?->title }}
                                    </td>

                                    <td>
                                        {{ $license->license_number }}
                                    </td>

                                    <td>
                                        {{ str(
                                            $license->license_type
                                        )->title() }}
                                    </td>

                                    <td>
                                        {{ $license->starts_at
                                            ?->format('d M Y')
                                        }}
                                    </td>

                                    <td>
                                        {{ $license->expires_at
                                            ?->format('d M Y')
                                            ?? 'No expiry'
                                        }}
                                    </td>

                                    <td>
                                        <x-library.license-status
                                            :status="$license->status"
                                        />
                                    </td>

                                    <td>

                                        <a
                                            href="{{ route(
                                                'school.library.licenses.show',
                                                $license
                                            ) }}"
                                            class="table-icon-button"
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


                {{ $licenses->links() }}

            @else

                <div class="empty-state">

                    <h2>
                        No book licences
                    </h2>

                    <p>
                        Your institution currently has no library licences.
                    </p>

                </div>

            @endif

        </div>

    </div>

</x-layouts.dashboard>