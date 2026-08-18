<x-layouts.dashboard title="Assignments — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Academic Activities
            </span>

            <h1>
                Assignments
            </h1>

            <p>
                Create and monitor literature assignments,
                assessments and learner completion.
            </p>

        </div>


        <div class="actions">

            <a
                href="{{ route(
                    'school.assignments.create'
                ) }}"
                class="button"
            >
                + New Assignment
            </a>

        </div>

    </div>


    @php

        $activeAssignments =
            $assignments
                ->getCollection()
                ->where(
                    'status',
                    'published'
                )
                ->count();


        $dueThisWeek =
            $assignments
                ->getCollection()
                ->filter(
                    fn ($assignment) =>
                        $assignment->due_at
                        &&
                        $assignment->due_at->isFuture()
                        &&
                        $assignment->due_at
                            ->lte(
                                now()->addDays(7)
                            )
                )
                ->count();


        $studentCount =
            $assignments
                ->getCollection()
                ->sum(
                    'students_count'
                );

    @endphp


    {{-- ================================================================
         METRICS
    ================================================================= --}}

    <div class="metric-grid">

        <article>

            <strong>
                {{ $activeAssignments }}
            </strong>

            <span>
                Active Assignments
            </span>

        </article>


        <article>

            <strong>
                {{ $dueThisWeek }}
            </strong>

            <span>
                Due This Week
            </span>

        </article>


        <article>

            <strong>
                {{ $studentCount }}
            </strong>

            <span>
                Learner Assignments
            </span>

        </article>

    </div>


    <div style="height:14px;"></div>


    {{-- ================================================================
         FILTERS
    ================================================================= --}}

    <div class="card">

        <form
            method="GET"
            action="{{ route(
                'school.assignments.index'
            ) }}"
            class="assignment-filters"
        >

            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search assignments..."
            >


            <select
                name="status"
            >

                <option value="">
                    All statuses
                </option>

                @foreach ([
                    'draft' =>
                        'Draft',

                    'published' =>
                        'Published',

                    'closed' =>
                        'Closed',

                    'archived' =>
                        'Archived',
                ] as $value => $label)

                    <option
                        value="{{ $value }}"
                        @selected(
                            request('status')
                            === $value
                        )
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>


            <button
                type="submit"
                class="button button-secondary button-small"
            >
                Filter
            </button>


            @if (
                request('search')
                ||
                request('status')
            )

                <a
                    href="{{ route(
                        'school.assignments.index'
                    ) }}"
                    class="button button-secondary button-small"
                >
                    Clear
                </a>

            @endif

        </form>

    </div>


    <div style="height:14px;"></div>


    {{-- ================================================================
         ASSIGNMENT LIST
    ================================================================= --}}

    <div class="card">

        <div class="row-between">

            <div>

                <h3>
                    Assignment Overview
                </h3>

                <p>
                    Review assignments created for your
                    classes and learners.
                </p>

            </div>

        </div>


        <div style="height:12px;"></div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>
                            Assignment
                        </th>

                        <th>
                            Class
                        </th>

                        <th>
                            Teacher
                        </th>

                        <th>
                            Students
                        </th>

                        <th>
                            Due Date
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse (
                        $assignments
                        as $assignment
                    )

                        <tr>

                            <td>

                                <div class="assignment-title">

                                    <strong>
                                        {{ $assignment->title }}
                                    </strong>


                                    @if (
                                        $assignment->book
                                            ?? false
                                    )

                                        <span>
                                            {{ $assignment
                                                ->book
                                                ->title
                                            }}
                                        </span>

                                    @endif

                                </div>

                            </td>


                            <td>

                                {{ $assignment
                                    ->schoolClass
                                    ?->name
                                    ?? '—'
                                }}

                            </td>


                            <td>

                                {{ $assignment
                                    ->creator
                                    ?->name
                                    ?? '—'
                                }}

                            </td>


                            <td>

                                {{ $assignment
                                    ->students_count
                                    ?? 0
                                }}

                            </td>


                            <td>

                                @if (
                                    $assignment->due_at
                                )

                                    <span
                                        class="{{ $assignment
                                            ->due_at
                                            ->isPast()
                                            &&
                                            $assignment->status
                                            === 'published'
                                                ? 'text-danger'
                                                : ''
                                        }}"
                                    >

                                        {{ $assignment
                                            ->due_at
                                            ->format(
                                                'd M Y'
                                            )
                                        }}

                                    </span>

                                @else

                                    —

                                @endif

                            </td>


                            <td>

                                <span
                                    class="
                                        assignment-status
                                        assignment-status--{{ $assignment->status }}
                                    "
                                >

                                    {{ ucfirst(
                                        $assignment->status
                                    ) }}

                                </span>

                            </td>


                            <td>

                                <a
                                    href="{{ route(
                                        'school.assignments.show',
                                        $assignment
                                    ) }}"
                                    class="table-link"
                                >
                                    View
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7">

                                <div class="empty-state">

                                    <h3>
                                        No assignments found
                                    </h3>

                                    <p>
                                        Create your first assignment
                                        to begin assigning literature
                                        to learners.
                                    </p>


                                    <a
                                        href="{{ route(
                                            'school.assignments.create'
                                        ) }}"
                                        class="button button-small"
                                    >
                                        Create Assignment
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    @if (
        $assignments
            ->hasPages()
    )

        <div class="pagination-shell">

            {{ $assignments->links() }}

        </div>

    @endif


    <style>

        .assignment-filters {
            display: grid;

            grid-template-columns:
                minmax(
                    240px,
                    1fr
                )
                180px
                auto
                auto;

            gap: 8px;
        }


        .assignment-filters input,
        .assignment-filters select {
            width: 100%;
        }


        .assignment-title {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }


        .assignment-title strong {
            color:
                var(--color-text);

            font-size:
                .58rem;
        }


        .assignment-title span {
            color:
                var(--color-text-muted);

            font-size:
                .49rem;
        }


        .assignment-status {
            display:
                inline-flex;

            align-items:
                center;

            padding:
                4px 7px;

            border-radius:
                999px;

            background:
                var(--color-surface-soft);

            color:
                var(--color-text-muted);

            font-size:
                .48rem;

            font-weight:
                750;

            text-transform:
                capitalize;
        }


        .assignment-status--published {
            color:
                var(--color-primary);
        }


        .assignment-status--closed,
        .assignment-status--archived {
            opacity:
                .7;
        }


        .table-link {
            color:
                var(--color-primary);

            font-size:
                .52rem;

            font-weight:
                750;

            text-decoration:
                none;
        }


        .pagination-shell {
            margin-top:
                16px;
        }


        .text-danger {
            color:
                var(--color-danger, #b42318);
        }


        @media (
            max-width: 750px
        ) {

            .assignment-filters {
                grid-template-columns:
                    1fr
                    1fr;
            }

        }


        @media (
            max-width: 480px
        ) {

            .assignment-filters {
                grid-template-columns:
                    1fr;
            }

        }

    </style>

</x-layouts.dashboard>