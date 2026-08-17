<x-layouts.dashboard title="Teachers — LiteraHub">

    <div class="dashboard-heading">

        <div>
            <span class="eyebrow">
                Staff Management
            </span>

            <h1>
                Teachers
            </h1>

            <p>
                Manage teachers and teaching access for
                {{ $school->name }}.
            </p>
        </div>


        <div class="actions">

            <a
                href="{{ route('school.teachers.create') }}"
                class="button"
            >
                + Add Teacher
            </a>

        </div>

    </div>


    {{-- =====================================================
         Teacher Summary
         ===================================================== --}}

    <div class="metric-grid">

        <article>

            <strong>
                {{ $totalTeachers ?? $teachers->total() }}
            </strong>

            <span>
                Total Teachers
            </span>

        </article>


        <article>

            <strong>
                {{ $activeTeachers ?? 0 }}
            </strong>

            <span>
                Active
            </span>

        </article>


        <article>

            <strong>
                {{ $classesAssigned ?? 0 }}
            </strong>

            <span>
                Classes Assigned
            </span>

        </article>

    </div>


    <div style="height: 14px;"></div>


    {{-- =====================================================
         Teacher Directory
         ===================================================== --}}

    <div class="card">

        <div class="row-between">

            <div>

                <h3>
                    Teaching Staff
                </h3>

                <p>
                    Search, review and manage teacher accounts
                    and class assignments.
                </p>

            </div>


            <form
                method="GET"
                action="{{ route('school.teachers.index') }}"
                style="width: min(100%, 320px);"
            >

                <div class="row">

                    <div style="flex: 1;">

                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search teachers..."
                            aria-label="Search teachers"
                        >

                    </div>


                    @if(request('search'))

                        <a
                            href="{{ route('school.teachers.index') }}"
                            class="button button-secondary button-small"
                        >
                            Clear
                        </a>

                    @endif

                </div>

            </form>

        </div>


        <div style="height: 12px;"></div>


        @if($teachers->count())

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>Teacher</th>
                            <th>Employee No.</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Classes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>

                    </thead>


                    <tbody>

                        @foreach($teachers as $teacher)

                            <tr>

                                {{-- Teacher --}}
                                <td>

                                    <div class="directory-person">

                                        <span
                                            class="directory-avatar"
                                            aria-hidden="true"
                                        >
                                            {{ strtoupper(
                                                substr(
                                                    $teacher->name,
                                                    0,
                                                    1
                                                )
                                            ) }}
                                        </span>


                                        <div class="directory-person__details">

                                            <div class="directory-person__name">

                                                <a
                                                    href="{{ route(
                                                        'school.teachers.show',
                                                        $teacher
                                                    ) }}"
                                                >
                                                    {{ $teacher->name }}
                                                </a>

                                            </div>


                                            <div class="directory-person__meta">
                                                Teaching Staff
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- Employee Number --}}
                                <td>

                                    <span class="table-value">
                                        {{ $teacher->pivot->reference_number ?? '—' }}
                                    </span>

                                </td>


                                {{-- Email --}}
                                <td>

                                    <span class="table-email">
                                        {{ $teacher->email }}
                                    </span>

                                </td>


                                {{-- Phone --}}
                                <td>

                                    <span class="table-value">
                                        {{ $teacher->phone ?? '—' }}
                                    </span>

                                </td>


                                {{-- Classes --}}
                                <td>

                                    @if($teacher->teachingClasses->count())

                                        <div class="teacher-class-list">

                                            @foreach(
                                                $teacher
                                                    ->teachingClasses
                                                    ->take(2)
                                                as $class
                                            )

                                                <a
                                                    href="{{ route(
                                                        'school.classes.show',
                                                        $class
                                                    ) }}"
                                                    class="badge badge-primary"
                                                >
                                                    {{ $class->name }}
                                                </a>

                                            @endforeach


                                            @if(
                                                $teacher
                                                    ->teachingClasses
                                                    ->count() > 2
                                            )

                                                <span
                                                    class="badge"
                                                    title="{{ $teacher->teachingClasses->count() - 2 }} more classes"
                                                >
                                                    +{{ $teacher
                                                        ->teachingClasses
                                                        ->count() - 2
                                                    }}
                                                </span>

                                            @endif

                                        </div>

                                    @else

                                        <span class="text-muted">
                                            No classes
                                        </span>

                                    @endif

                                </td>


                                {{-- Status --}}
                                <td>

                                    @php
                                        $status =
                                            $teacher->pivot->status
                                            ?? $teacher->status
                                            ?? 'inactive';

                                        $badgeClass =
                                            match($status) {
                                                'active'
                                                    => 'badge-success',

                                                'suspended'
                                                    => 'badge-warning',

                                                default
                                                    => 'badge-danger',
                                            };
                                    @endphp


                                    <span
                                        class="badge {{ $badgeClass }}"
                                    >
                                        {{ ucfirst($status) }}
                                    </span>

                                </td>


                                {{-- Actions --}}
                                <td>

                                    <div class="table-icon-actions">

                                        {{-- View --}}
                                        <a
                                            href="{{ route(
                                                'school.teachers.show',
                                                $teacher
                                            ) }}"
                                            class="table-icon-button"
                                            title="View teacher"
                                            aria-label="View {{ $teacher->name }}"
                                        >

                                            <svg
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6S2.25 12 2.25 12Z"
                                                />

                                                <circle
                                                    cx="12"
                                                    cy="12"
                                                    r="2.75"
                                                />
                                            </svg>

                                        </a>


                                        {{-- Edit --}}
                                        <a
                                            href="{{ route(
                                                'school.teachers.edit',
                                                $teacher
                                            ) }}"
                                            class="table-icon-button"
                                            title="Edit teacher"
                                            aria-label="Edit {{ $teacher->name }}"
                                        >

                                            <svg
                                                viewBox="0 0 24 24"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="M13.5 6.5 17.5 10.5"
                                                />

                                                <path
                                                    d="M4 20l4.25-1 9.8-9.8a2 2 0 0 0 0-2.82l-.43-.43a2 2 0 0 0-2.82 0L5 14.75 4 20Z"
                                                />
                                            </svg>

                                        </a>


                                        {{-- Deactivate --}}
                                        @if(
                                            ($teacher->pivot->status
                                                ?? $teacher->status)
                                            === 'active'
                                        )

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'school.teachers.destroy',
                                                    $teacher
                                                ) }}"
                                                class="table-icon-form"
                                            >

                                                @csrf
                                                @method('DELETE')


                                                <button
                                                    type="submit"
                                                    class="table-icon-button table-icon-button--danger"
                                                    title="Deactivate teacher"
                                                    aria-label="Deactivate {{ $teacher->name }}"
                                                    data-confirm="Deactivate {{ $teacher->name }}?"
                                                >

                                                    <svg
                                                        viewBox="0 0 24 24"
                                                        aria-hidden="true"
                                                    >
                                                        <circle
                                                            cx="12"
                                                            cy="12"
                                                            r="9"
                                                        />

                                                        <path
                                                            d="M8 12h8"
                                                        />
                                                    </svg>

                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if($teachers->hasPages())

                <div class="pagination-shell">

                    {{ $teachers
                        ->withQueryString()
                        ->links()
                    }}

                </div>

            @endif

        @else

            <div class="empty-state">

                @if(request('search'))

                    <h3>
                        No matching teachers
                    </h3>

                    <p>
                        No teaching staff matched
                        "{{ request('search') }}".
                    </p>

                    <a
                        href="{{ route('school.teachers.index') }}"
                        class="button button-secondary button-small"
                    >
                        Clear Search
                    </a>

                @else

                    <h3>
                        No teachers yet
                    </h3>

                    <p>
                        Add your teaching staff to begin
                        assigning classes, literature resources
                        and learner activities.
                    </p>

                    <a
                        href="{{ route('school.teachers.create') }}"
                        class="button button-small"
                    >
                        + Add First Teacher
                    </a>

                @endif

            </div>

        @endif

    </div>

</x-layouts.dashboard>