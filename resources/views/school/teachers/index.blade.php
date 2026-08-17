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

                                    <div class="student-cell">

                                        <span class="user-avatar">
                                            {{ strtoupper(
                                                substr(
                                                    $teacher->name,
                                                    0,
                                                    1
                                                )
                                            ) }}
                                        </span>


                                        <div>

                                            <a
                                                href="{{ route(
                                                    'school.teachers.show',
                                                    $teacher
                                                ) }}"
                                            >
                                                <strong>
                                                    {{ $teacher->name }}
                                                </strong>
                                            </a>


                                            <small>
                                                Teaching Staff
                                            </small>

                                        </div>

                                    </div>

                                </td>


                                {{-- Employee Number --}}
                                <td>

                                    {{ $teacher->pivot->reference_number
                                        ?? '—'
                                    }}

                                </td>


                                {{-- Email --}}
                                <td>

                                    {{ $teacher->email }}

                                </td>


                                {{-- Phone --}}
                                <td>

                                    {{ $teacher->phone ?? '—' }}

                                </td>


                                {{-- Classes --}}
                                <td>

                                    @if($teacher->teachingClasses->count())

                                        <div class="teacher-class-list">

                                            @foreach(
                                                $teacher
                                                    ->teachingClasses
                                                    ->take(3)
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
                                                    ->count() > 3
                                            )

                                                <span
                                                    class="badge"
                                                    title="{{ $teacher->teachingClasses->count() - 3 }} more classes"
                                                >
                                                    +{{ $teacher
                                                        ->teachingClasses
                                                        ->count() - 3
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

                                    <div class="table-actions">

                                        <a
                                            href="{{ route(
                                                'school.teachers.show',
                                                $teacher
                                            ) }}"
                                            class="button button-secondary button-small"
                                        >
                                            View
                                        </a>


                                        <a
                                            href="{{ route(
                                                'school.teachers.edit',
                                                $teacher
                                            ) }}"
                                            class="button button-ghost button-small"
                                        >
                                            Edit
                                        </a>


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
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="button button-ghost button-small"
                                                    data-confirm="Deactivate {{ $teacher->name }}?"
                                                >
                                                    Deactivate
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