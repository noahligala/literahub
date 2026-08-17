<x-layouts.dashboard>

    <div class="page-shell">

        <div class="page-header">

            <div>
                <span class="eyebrow">
                    Library Access
                </span>

                <h1>
                    Book Access Requests
                </h1>

                <p>
                    Review and track requests for books outside
                    a student's normal class allocation.
                </p>
            </div>

        </div>


        <div class="card">

            @if ($requests->count())

                <div class="table-wrapper">

                    <table class="table-condensed">

                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Book</th>
                                <th>Reason</th>
                                <th>Requested</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>


                        <tbody>

                            @foreach ($requests as $accessRequest)

                                <tr>

                                    <td>
                                        {{ $accessRequest->student?->name }}
                                    </td>

                                    <td>
                                        {{ $accessRequest->book?->title }}
                                    </td>

                                    <td>
                                        {{ \Illuminate\Support\Str::limit(
                                            $accessRequest->reason,
                                            65
                                        ) }}
                                    </td>

                                    <td>
                                        {{ $accessRequest->created_at
                                            ?->format('d M Y')
                                        }}
                                    </td>

                                    <td>

                                        @php
                                            $statusClass =
                                                match (
                                                    $accessRequest->status
                                                ) {
                                                    'approved' =>
                                                        'badge--success',

                                                    'pending' =>
                                                        'badge--warning',

                                                    'rejected',
                                                    'expired' =>
                                                        'badge--danger',

                                                    default =>
                                                        'badge--muted',
                                                };
                                        @endphp

                                        <span class="badge {{ $statusClass }}">
                                            {{ str(
                                                $accessRequest->status
                                            )->title() }}
                                        </span>

                                    </td>

                                    <td>

                                        <a
                                            href="{{ route(
                                                'school.library.requests.show',
                                                $accessRequest
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


                {{ $requests->links() }}

            @else

                <div class="empty-state">

                    <h2>
                        No access requests
                    </h2>

                    <p>
                        There are currently no requests matching this view.
                    </p>

                </div>

            @endif

        </div>

    </div>

</x-layouts.dashboard>