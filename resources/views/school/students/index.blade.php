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

                                    <div class="student-cell">

                                        <span class="user-avatar">
                                            {{ strtoupper(
                                                substr(
                                                    $student->name,
                                                    0,
                                                    1
                                                )
                                            ) }}
                                        </span>


                                        <div>

                                            <a
                                                href="{{ route(
                                                    'school.students.show',
                                                    $student
                                                ) }}"
                                            >
                                                <strong>
                                                    {{ $student->name }}
                                                </strong>
                                            </a>

                                            @if($student->phone)

                                                <small>
                                                    {{ $student->phone }}
                                                </small>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- Admission Number --}}
                                <td>

                                    {{ $student->pivot->reference_number
                                        ?? '—'
                                    }}

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
                                        >
                                            {{ $studentClass->name }}
                                        </a>

                                    @else

                                        <span class="text-muted">
                                            Not assigned
                                        </span>

                                    @endif

                                </td>


                                {{-- Email --}}
                                <td>

                                    {{ $student->email }}

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

                                    <div class="table-actions">

                                        <a
                                            href="{{ route(
                                                'school.students.show',
                                                $student
                                            ) }}"
                                            class="button button-secondary button-small"
                                        >
                                            View
                                        </a>


                                        <a
                                            href="{{ route(
                                                'school.students.edit',
                                                $student
                                            ) }}"
                                            class="button button-ghost button-small"
                                        >
                                            Edit
                                        </a>


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
                                            >

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="button button-ghost button-small"
                                                    data-confirm="Deactivate {{ $student->name }}?"
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