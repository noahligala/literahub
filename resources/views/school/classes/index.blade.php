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

                <table class="table-condensed">

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

                                {{-- Stream --}}
                                <td>

                                    <div class="directory-person">

                                        <span
                                            class="directory-avatar"
                                            aria-hidden="true"
                                        >
                                            {{ strtoupper(
                                                substr(
                                                    $stream->name,
                                                    0,
                                                    1
                                                )
                                            ) }}
                                        </span>


                                        <div class="directory-person__details">

                                            <div class="directory-person__name">

                                                <strong>
                                                    {{ $stream->name }}
                                                </strong>

                                            </div>

                                            <div class="directory-person__meta">
                                                Stream
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- Class --}}
                                <td>

                                    <a
                                        href="{{ route(
                                            'school.classes.show',
                                            $stream->schoolClass
                                        ) }}"
                                        class="table-link"
                                    >
                                        {{ $stream->schoolClass->name }}
                                    </a>

                                </td>


                                {{-- Class Teacher --}}
                                <td>

                                    @if($stream->teacher)

                                        <div class="directory-person directory-person--small">

                                            <span
                                                class="directory-avatar directory-avatar--small"
                                            >
                                                {{ strtoupper(
                                                    substr(
                                                        $stream->teacher->name,
                                                        0,
                                                        1
                                                    )
                                                ) }}
                                            </span>

                                            <div class="directory-person__details">

                                                <a
                                                    href="{{ route(
                                                        'school.teachers.show',
                                                        $stream->teacher
                                                    ) }}"
                                                    class="table-link"
                                                >
                                                    {{ $stream->teacher->name }}
                                                </a>

                                            </div>

                                        </div>

                                    @else

                                        <span class="text-muted">
                                            Not assigned
                                        </span>

                                    @endif

                                </td>


                                {{-- Status --}}
                                <td>

                                    <span
                                        class="badge {{
                                            $stream->status === 'active'
                                                ? 'badge-success'
                                                : 'badge-danger'
                                        }}"
                                    >
                                        {{ ucfirst($stream->status) }}
                                    </span>

                                </td>


                                {{-- Actions --}}
                                <td>

                                    <div class="table-icon-actions">

                                        {{-- Edit --}}
                                        <a
                                            href="{{ route(
                                                'school.streams.edit',
                                                $stream
                                            ) }}"
                                            class="table-icon-button"
                                            title="Edit stream"
                                            aria-label="Edit {{ $stream->name }}"
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


                                        {{-- Delete --}}
                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'school.streams.destroy',
                                                $stream
                                            ) }}"
                                            class="table-icon-form"
                                        >

                                            @csrf
                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                class="table-icon-button table-icon-button--danger"
                                                title="Delete stream"
                                                aria-label="Delete {{ $stream->name }}"
                                                data-confirm="Delete stream {{ $stream->name }}?"
                                            >

                                                <svg
                                                    viewBox="0 0 24 24"
                                                    aria-hidden="true"
                                                >
                                                    <path
                                                        d="M4 7h16"
                                                    />

                                                    <path
                                                        d="M9 7V4h6v3"
                                                    />

                                                    <path
                                                        d="M7 7l1 13h8l1-13"
                                                    />

                                                    <path
                                                        d="M10 11v5"
                                                    />

                                                    <path
                                                        d="M14 11v5"
                                                    />
                                                </svg>

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