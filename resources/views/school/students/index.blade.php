<x-layouts.dashboard title="Students — LiteraHub">

    <div class="dashboard-heading">

        <div>
            <span class="eyebrow">
                School Management
            </span>

            <h1>
                Students
            </h1>

            <p>
                Manage learners registered under
                {{ $school->name }}.
            </p>
        </div>

        <div class="actions">

            <a
                href="{{ route('school.students.create') }}"
                class="button"
            >
                + Add Student
            </a>

        </div>

    </div>


    {{-- =====================================================
         Student Summary
         ===================================================== --}}

    <div class="metric-grid">

        <article>

            <strong>
                {{ $totalStudents ?? $students->total() }}
            </strong>

            <span>
                Total Students
            </span>

        </article>


        <article>

            <strong>
                {{ $activeStudents ?? 0 }}
            </strong>

            <span>
                Active
            </span>

        </article>


        <article>

            <strong>
                {{ $inactiveStudents ?? 0 }}
            </strong>

            <span>
                Inactive / Suspended
            </span>

        </article>

    </div>


    <div style="height: 14px;"></div>


    {{-- =====================================================
         Student Directory
         ===================================================== --}}

    <div class="card">

        <div class="row-between">

            <div>

                <h3>
                    Student Directory
                </h3>

                <p>
                    Search, view and manage student accounts.
                </p>

            </div>


            {{-- Search --}}
            <form
                method="GET"
                action="{{ route('school.students.index') }}"
                style="width: min(100%, 320px);"
            >

                <div class="row">

                    <div style="flex: 1;">

                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search students..."
                            aria-label="Search students"
                        >

                    </div>

                    @if(request('search'))

                        <a
                            href="{{ route('school.students.index') }}"
                            class="button button-secondary button-small"
                        >
                            Clear
                        </a>

                    @endif

                </div>

            </form>

        </div>


        <div style="height: 12px;"></div>


        {{-- =================================================
             Students Table
             ================================================= --}}

        @if($students->count())

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>Student</th>
                            <th>Admission No.</th>
                            <th>Class</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>

                    </thead>


                    <tbody>

                    @foreach($students as $student)

                        <tr>

                            {{-- Student --}}
                            <td>

                                <div class="directory-person">

                                    <span
                                        class="directory-avatar"
                                        aria-hidden="true"
                                    >
                                        {{ strtoupper(
                                            substr(
                                                $student->name,
                                                0,
                                                1
                                            )
                                        ) }}
                                    </span>


                                    <div class="directory-person__details">

                                        <div class="directory-person__name">

                                            <a
                                                href="{{ route(
                                                    'school.students.show',
                                                    $student
                                                ) }}"
                                            >
                                                {{ $student->name }}
                                            </a>

                                        </div>


                                        @if($student->phone)

                                            <div class="directory-person__meta">
                                                {{ $student->phone }}
                                            </div>

                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- Admission Number --}}
                            <td>

                                <span class="table-value">
                                    {{ $student->pivot->reference_number ?? '—' }}
                                </span>

                            </td>


                            {{-- Class --}}
                            <td>

                                @php
                                    $studentClass =
                                        $student
                                            ->studentClasses
                                            ->first();
                                @endphp


                                @if($studentClass)

                                    <a
                                        href="{{ route(
                                            'school.classes.show',
                                            $studentClass
                                        ) }}"
                                        class="table-link"
                                    >
                                        {{ $studentClass->name }}
                                    </a>


                                    @if($studentClass->pivot?->stream_id)

                                        @php
                                            $studentStream =
                                                $studentClass
                                                    ->streams
                                                    ->firstWhere(
                                                        'id',
                                                        $studentClass
                                                            ->pivot
                                                            ->stream_id
                                                    );
                                        @endphp


                                        @if($studentStream)

                                            <span class="table-secondary">
                                                {{ $studentStream->name }}
                                            </span>

                                        @endif

                                    @endif

                                @else

                                    <span class="text-muted">
                                        Not assigned
                                    </span>

                                @endif

                            </td>


                            {{-- Email --}}
                            <td>

                                <span class="table-email">
                                    {{ $student->email }}
                                </span>

                            </td>


                            {{-- Status --}}
                            <td>

                                @php
                                    $status =
                                        $student->pivot->status
                                        ?? $student->status
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
                                            'school.students.show',
                                            $student
                                        ) }}"
                                        class="table-icon-button"
                                        title="View student"
                                        aria-label="View {{ $student->name }}"
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
                                            'school.students.edit',
                                            $student
                                        ) }}"
                                        class="table-icon-button"
                                        title="Edit student"
                                        aria-label="Edit {{ $student->name }}"
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
                                        ($student->pivot->status
                                            ?? $student->status)
                                        === 'active'
                                    )

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'school.students.destroy',
                                                $student
                                            ) }}"
                                            class="table-icon-form"
                                        >

                                            @csrf
                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                class="table-icon-button table-icon-button--danger"
                                                title="Deactivate student"
                                                aria-label="Deactivate {{ $student->name }}"
                                                data-confirm="Deactivate {{ $student->name }}?"
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
            @if($students->hasPages())

                <div class="pagination-shell">

                    {{ $students->withQueryString()->links() }}

                </div>

            @endif

        @else

            {{-- =================================================
                 Empty State
                 ================================================= --}}

            <div class="empty-state">

                @if(request('search'))

                    <h3>
                        No matching students
                    </h3>

                    <p>
                        No learners matched
                        "{{ request('search') }}".
                    </p>

                    <a
                        href="{{ route('school.students.index') }}"
                        class="button button-secondary button-small"
                    >
                        Clear Search
                    </a>

                @else

                    <h3>
                        No students yet
                    </h3>

                    <p>
                        Add your first learner to begin
                        managing classes, assignments and
                        access to LiteraHub resources.
                    </p>

                    <a
                        href="{{ route('school.students.create') }}"
                        class="button button-small"
                    >
                        + Add First Student
                    </a>

                @endif

            </div>

        @endif

    </div>

</x-layouts.dashboard>