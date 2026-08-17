<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>
                <span class="eyebrow">
                    Creators
                </span>

                <h1>
                    Authors
                </h1>

                <p>
                    Manage author profiles, publisher relationships,
                    catalogue works and intellectual-property permissions.
                </p>
            </div>


            @can('create', App\Models\Author::class)

                <a
                    href="{{ route('authors.create') }}"
                    class="btn btn--primary"
                >
                    Add Author
                </a>

            @endcan

        </div>


        <div class="card">

            <form
                method="GET"
                action="{{ route('authors.index') }}"
                class="directory-filters"
            >

                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search author..."
                >


                <select name="status">

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
                        value="verified"
                        @selected(request('status') === 'verified')
                    >
                        Verified
                    </option>

                    <option
                        value="suspended"
                        @selected(request('status') === 'suspended')
                    >
                        Suspended
                    </option>

                </select>


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
                        href="{{ route('authors.index') }}"
                        class="btn btn--ghost"
                    >
                        Clear
                    </a>

                @endif

            </form>

        </div>


        <div class="card">

            @if ($authors->count())

                <div class="table-wrapper">

                    <table class="table-condensed">

                        <thead>
                            <tr>
                                <th>Author</th>
                                <th>Publisher</th>
                                <th>Books</th>
                                <th>User Account</th>
                                <th>Status</th>
                                <th>Updated</th>
                                <th></th>
                            </tr>
                        </thead>


                        <tbody>

                            @foreach ($authors as $author)

                                @php
                                    $statusClass = match ($author->status) {
                                        'verified' => 'badge--success',
                                        'pending' => 'badge--warning',
                                        'suspended' => 'badge--danger',
                                        default => 'badge--muted',
                                    };
                                @endphp


                                <tr>

                                    <td>

                                        <div class="directory-person">

                                            <span class="directory-avatar">

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


                                            <div class="directory-person__details">

                                                <div class="directory-person__name">

                                                    <a
                                                        href="{{ route(
                                                            'authors.show',
                                                            $author
                                                        ) }}"
                                                    >
                                                        {{ $author->name }}
                                                    </a>

                                                </div>

                                                <span class="directory-person__meta">
                                                    Author profile
                                                </span>

                                            </div>

                                        </div>

                                    </td>


                                    <td>
                                        {{ $author->publisher?->name
                                            ?? 'Independent'
                                        }}
                                    </td>


                                    <td>
                                        {{ number_format(
                                            $author->books_count
                                            ?? $author->books?->count()
                                            ?? 0
                                        ) }}
                                    </td>


                                    <td>
                                        {{ $author->user?->email
                                            ?? 'Not linked'
                                        }}
                                    </td>


                                    <td>
                                        <span class="badge {{ $statusClass }}">
                                            {{ str($author->status)->title() }}
                                        </span>
                                    </td>


                                    <td>
                                        {{ $author->updated_at?->format('d M Y') }}
                                    </td>


                                    <td>

                                        <div class="table-icon-actions">

                                            <a
                                                href="{{ route(
                                                    'authors.show',
                                                    $author
                                                ) }}"
                                                class="table-icon-button"
                                            >
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </a>


                                            @can('update', $author)

                                                <a
                                                    href="{{ route(
                                                        'authors.edit',
                                                        $author
                                                    ) }}"
                                                    class="table-icon-button"
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


                @if (method_exists($authors, 'links'))

                    <div class="pagination-shell">
                        {{ $authors->links() }}
                    </div>

                @endif

            @else

                <div class="empty-state">

                    <h2>
                        No authors found
                    </h2>

                    <p>
                        Register an author or adjust your current filters.
                    </p>

                </div>

            @endif

        </div>

    </div>


    <style>
        .directory-filters {
            display: grid;
            grid-template-columns: minmax(280px, 1fr) 180px auto auto;
            gap: 8px;
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