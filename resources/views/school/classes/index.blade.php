<x-layouts.dashboard title="Classes — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Academic Structure
            </span>

            <h1>
                Classes & Streams
            </h1>

            <p>
                Organize learners into classes, streams
                and learning groups for {{ $school->name }}.
            </p>

        </div>


        <div class="actions">

            <a
                href="{{ route('school.classes.create') }}"
                class="button"
            >
                + Add Class
            </a>

        </div>

    </div>


    {{-- =====================================================
         Metrics
         ===================================================== --}}

    <div class="metric-grid">

        <article>

            <strong>
                {{ $totalClasses ?? 0 }}
            </strong>

            <span>
                Total Classes
            </span>

        </article>


        <article>

            <strong>
                {{ $activeStudents ?? 0 }}
            </strong>

            <span>
                Active Students
            </span>

        </article>


        <article>

            <strong>
                {{ $teachersAssigned ?? 0 }}
            </strong>

            <span>
                Teachers Assigned
            </span>

        </article>

    </div>


    <div style="height: 14px;"></div>


    {{-- =====================================================
         Search / Filters
         ===================================================== --}}

    <div class="card">

        <form
            method="GET"
            action="{{ route('school.classes.index') }}"
        >

            <div class="row-between">

                <div>

                    <h3>
                        Class Directory
                    </h3>

                    <p>
                        View and manage classes and streams
                        within your institution.
                    </p>

                </div>


                <div
                    class="row"
                    style="width: min(100%, 480px);"
                >

                    <div style="flex: 1;">

                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search classes..."
                        >

                    </div>


                    <div style="width: min(100%, 150px);">

                        <select name="status">

                            <option value="">
                                All Statuses
                            </option>

                            <option
                                value="active"
                                @selected(
                                    request('status') === 'active'
                                )
                            >
                                Active
                            </option>

                            <option
                                value="inactive"
                                @selected(
                                    request('status') === 'inactive'
                                )
                            >
                                Inactive
                            </option>

                        </select>

                    </div>


                    <button
                        type="submit"
                        class="button button-small"
                    >
                        Filter
                    </button>


                    @if(
                        request('search')
                        || request('status')
                    )

                        <a
                            href="{{ route(
                                'school.classes.index'
                            ) }}"
                            class="button button-secondary button-small"
                        >
                            Clear
                        </a>

                    @endif

                </div>

            </div>

        </form>

    </div>


    <div style="height: 14px;"></div>


    {{-- =====================================================
         Classes
         ===================================================== --}}

    @if($classes->count())

        <section class="cards">

            @foreach($classes as $class)

                <article>

                    <span class="eyebrow">
                        Class
                    </span>


                    <div class="row-between">

                        <div>

                            <h3>
                                {{ $class->name }}
                            </h3>

                            <p>

                                @if($class->academic_year)

                                    {{ $class->academic_year }}

                                @else

                                    Academic class group

                                @endif

                                @if($class->code)
                                    · {{ $class->code }}
                                @endif

                            </p>

                        </div>


                        <span
                            class="badge
                                {{
                                    $class->status === 'active'
                                        ? 'badge-success'
                                        : 'badge-danger'
                                }}"
                        >
                            {{ ucfirst($class->status) }}
                        </span>

                    </div>


                    <div style="height: 8px;"></div>


                    <div class="class-stats">

                        <div>

                            <strong>
                                {{ $class->students_count ?? 0 }}
                            </strong>

                            <span>
                                Students
                            </span>

                        </div>


                        <div>

                            <strong>
                                {{ $class->teachers_count ?? 0 }}
                            </strong>

                            <span>
                                Teachers
                            </span>

                        </div>


                        <div>

                            <strong>
                                {{ $class->assignments_count ?? 0 }}
                            </strong>

                            <span>
                                Assignments
                            </span>

                        </div>


                        <div>

                            <strong>
                                {{ $class->streams_count ?? 0 }}
                            </strong>

                            <span>
                                Streams
                            </span>

                        </div>

                    </div>


                    <div class="actions">

                        <a
                            href="{{ route(
                                'school.classes.show',
                                $class
                            ) }}"
                            class="button button-secondary button-small"
                        >
                            View Class
                        </a>


                        <a
                            href="{{ route(
                                'school.classes.edit',
                                $class
                            ) }}"
                            class="button button-ghost button-small"
                        >
                            Edit
                        </a>


                        <a
                            href="{{ route(
                                'school.streams.create',
                                $class
                            ) }}"
                            class="button button-ghost button-small"
                        >
                            + Stream
                        </a>

                    </div>

                </article>

            @endforeach

        </section>


        @if($classes->hasPages())

            <div class="pagination-shell">

                {{ $classes
                    ->withQueryString()
                    ->links()
                }}

            </div>

        @endif

    @else

        <div class="card">

            <div class="empty-state">

                @if(
                    request('search')
                    || request('status')
                )

                    <h3>
                        No matching classes
                    </h3>

                    <p>
                        No classes match the current filters.
                    </p>

                    <a
                        href="{{ route(
                            'school.classes.index'
                        ) }}"
                        class="button button-secondary button-small"
                    >
                        Clear Filters
                    </a>

                @else

                    <h3>
                        No classes yet
                    </h3>

                    <p>
                        Create your first class before
                        adding students, teachers and streams.
                    </p>

                    <a
                        href="{{ route(
                            'school.classes.create'
                        ) }}"
                        class="button button-small"
                    >
                        + Add First Class
                    </a>

                @endif

            </div>

        </div>

    @endif


    <div style="height: 14px;"></div>


    {{-- =====================================================
         Streams
         ===================================================== --}}

    <div class="card">

        <div class="row-between">

            <div>

                <h3>
                    Streams
                </h3>

                <p>
                    Streams and learning groups configured
                    across your institution.
                </p>

            </div>

        </div>


        <div style="height: 10px;"></div>


        @if($streams->count())

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>Stream</th>
                            <th>Class</th>
                            <th>Class Teacher</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>

                    </thead>


                    <tbody>

                        @foreach($streams as $stream)

                            <tr>

                                <td>
                                    <strong>
                                        {{ $stream->name }}
                                    </strong>
                                </td>


                                <td>

                                    <a
                                        href="{{ route(
                                            'school.classes.show',
                                            $stream->schoolClass
                                        ) }}"
                                    >
                                        {{ $stream
                                            ->schoolClass
                                            ->name
                                        }}
                                    </a>

                                </td>


                                <td>

                                    @if($stream->teacher)

                                        <a
                                            href="{{ route(
                                                'school.teachers.show',
                                                $stream->teacher
                                            ) }}"
                                        >
                                            {{ $stream->teacher->name }}
                                        </a>

                                    @else

                                        <span class="text-muted">
                                            Not assigned
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    <span
                                        class="badge
                                            {{
                                                $stream->status === 'active'
                                                    ? 'badge-success'
                                                    : 'badge-danger'
                                            }}"
                                    >
                                        {{ ucfirst(
                                            $stream->status
                                        ) }}
                                    </span>

                                </td>


                                <td>

                                    <div class="table-actions">

                                        <a
                                            href="{{ route(
                                                'school.streams.edit',
                                                $stream
                                            ) }}"
                                            class="button button-secondary button-small"
                                        >
                                            Edit
                                        </a>


                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'school.streams.destroy',
                                                $stream
                                            ) }}"
                                        >

                                            @csrf
                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                class="button button-ghost button-small"
                                                data-confirm="Delete stream {{ $stream->name }}?"
                                            >
                                                Delete
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty-state">

                <h3>
                    No streams yet
                </h3>

                <p>
                    Streams are created within individual
                    classes. Open a class or use its
                    "+ Stream" button to create one.
                </p>

            </div>

        @endif

    </div>

</x-layouts.dashboard>