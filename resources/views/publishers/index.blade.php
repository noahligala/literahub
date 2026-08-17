<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>
                <span class="eyebrow">
                    Rights Holders
                </span>

                <h1>
                    Publishers
                </h1>

                <p>
                    Manage publishing partners, their authors,
                    catalogue titles and institutional licensing activity.
                </p>
            </div>


            @can('create', App\Models\Publisher::class)

                <a
                    href="{{ route('publishers.create') }}"
                    class="btn btn--primary"
                >
                    Add Publisher
                </a>

            @endcan

        </div>


        {{-- Search --}}
        <div class="card">

            <form
                method="GET"
                action="{{ route('publishers.index') }}"
                class="directory-filters"
            >

                <div>

                    <label
                        for="search"
                        class="sr-only"
                    >
                        Search publishers
                    </label>

                    <input
                        id="search"
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search publisher name, email or registration number..."
                    >

                </div>


                <div>

                    <label
                        for="status"
                        class="sr-only"
                    >
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                    >
                        <option value="">
                            All statuses
                        </option>

                        <option
                            value="pending"
                            @selected(request('status') === 'pending')
                        >
                            Pending
                        </option>

                        <option
                            value="active"
                            @selected(request('status') === 'active')
                        >
                            Active
                        </option>

                        <option
                            value="suspended"
                            @selected(request('status') === 'suspended')
                        >
                            Suspended
                        </option>
                    </select>

                </div>


                <button
                    type="submit"
                    class="btn btn--secondary"
                >
                    Filter
                </button>


                @if (
                    request('search')
                    || request('status')
                )

                    <a
                        href="{{ route('publishers.index') }}"
                        class="btn btn--ghost"
                    >
                        Clear
                    </a>

                @endif

            </form>

        </div>


        <div class="card">

            @if ($publishers->count())

                <div class="table-wrapper">

                    <table class="table-condensed">

                        <thead>
                            <tr>
                                <th>Publisher</th>
                                <th>Registration</th>
                                <th>Authors</th>
                                <th>Books</th>
                                <th>Licences</th>
                                <th>Status</th>
                                <th>Updated</th>
                                <th></th>
                            </tr>
                        </thead>


                        <tbody>

                            @foreach ($publishers as $publisher)

                                @php
                                    $initials = collect(
                                        preg_split(
                                            '/\s+/',
                                            trim($publisher->name)
                                        )
                                    )
                                        ->filter()
                                        ->take(2)
                                        ->map(
                                            fn ($word) =>
                                                strtoupper(
                                                    mb_substr($word, 0, 1)
                                                )
                                        )
                                        ->implode('');

                                    $statusClass = match ($publisher->status) {
                                        'active' => 'badge--success',
                                        'pending' => 'badge--warning',
                                        'suspended' => 'badge--danger',
                                        default => 'badge--muted',
                                    };
                                @endphp


                                <tr>

                                    <td>

                                        <div class="directory-person">

                                            <span class="directory-avatar">
                                                {{ $initials }}
                                            </span>

                                            <div class="directory-person__details">

                                                <div class="directory-person__name">

                                                    <a
                                                        href="{{ route(
                                                            'publishers.show',
                                                            $publisher
                                                        ) }}"
                                                    >
                                                        {{ $publisher->name }}
                                                    </a>

                                                </div>

                                                <span class="directory-person__meta">
                                                    {{ $publisher->email ?? 'No email' }}
                                                </span>

                                            </div>

                                        </div>

                                    </td>


                                    <td>
                                        <span class="table-value">
                                            {{ $publisher->registration_number ?? '—' }}
                                        </span>
                                    </td>


                                    <td>
                                        <span class="table-value">
                                            {{ number_format(
                                                $publisher->authors_count
                                                ?? $publisher->authors?->count()
                                                ?? 0
                                            ) }}
                                        </span>
                                    </td>


                                    <td>
                                        <span class="table-value">
                                            {{ number_format(
                                                $publisher->books_count
                                                ?? $publisher->books?->count()
                                                ?? 0
                                            ) }}
                                        </span>
                                    </td>


                                    <td>
                                        <span class="table-value">
                                            {{ number_format(
                                                $publisher->school_book_licenses_count
                                                ?? $publisher->schoolBookLicenses?->count()
                                                ?? 0
                                            ) }}
                                        </span>
                                    </td>


                                    <td>
                                        <span class="badge {{ $statusClass }}">
                                            {{ str($publisher->status)->title() }}
                                        </span>
                                    </td>


                                    <td>
                                        <span class="table-value">
                                            {{ $publisher->updated_at?->format('d M Y') }}
                                        </span>
                                    </td>


                                    <td>

                                        <div class="table-icon-actions">

                                            <a
                                                href="{{ route(
                                                    'publishers.show',
                                                    $publisher
                                                ) }}"
                                                class="table-icon-button"
                                                title="View publisher"
                                            >
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </a>


                                            @can('update', $publisher)

                                                <a
                                                    href="{{ route(
                                                        'publishers.edit',
                                                        $publisher
                                                    ) }}"
                                                    class="table-icon-button"
                                                    title="Edit publisher"
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


                @if (method_exists($publishers, 'links'))

                    <div class="pagination-shell">
                        {{ $publishers->links() }}
                    </div>

                @endif

            @else

                <div class="empty-state">

                    <h2>
                        No publishers found
                    </h2>

                    <p>
                        Add a publishing partner or adjust your current filters.
                    </p>


                    @can('create', App\Models\Publisher::class)

                        <a
                            href="{{ route('publishers.create') }}"
                            class="btn btn--primary"
                        >
                            Add Publisher
                        </a>

                    @endcan

                </div>

            @endif

        </div>

    </div>


    <style>
        .directory-filters {
            display: grid;
            grid-template-columns: minmax(280px, 1fr) 180px auto auto;
            gap: 8px;
            align-items: end;
        }

        .directory-filters input,
        .directory-filters select {
            width: 100%;
        }

        .pagination-shell {
            margin-top: 15px;
        }

        @media (max-width: 760px) {
            .directory-filters {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 520px) {
            .directory-filters {
                grid-template-columns: 1fr;
            }
        }
    </style>

</x-layouts.dashboard>