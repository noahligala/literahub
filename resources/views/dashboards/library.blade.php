<x-layouts.dashboard title="My Library — LiteraHub">

    @php

        $user = auth()->user();

        $school = $user
            ->activeSchools()
            ->first();


        $isStudent =
            $user->hasRole(
                'student'
            );


        $isIndividual =
            $user->hasRole(
                'individual_subscriber'
            );


        /*
        |--------------------------------------------------------------------------
        | Student Assignment Metrics
        |--------------------------------------------------------------------------
        */

        $totalAssignments = 0;
        $submittedAssignments = 0;
        $gradedAssignments = 0;
        $overdueAssignments = 0;


        if (
            $isStudent
            && $school
        ) {

            $assignmentBase = $user
                ->assignments()
                ->where(
                    'assignments.school_id',
                    $school->id
                )
                ->whereIn(
                    'assignments.status',
                    [
                        'published',
                        'closed',
                    ]
                );


            $totalAssignments = (
                clone $assignmentBase
            )->count();


            $submittedAssignments =
                $user
                    ->assignmentSubmissions()
                    ->whereHas(
                        'assignment',
                        function ($query) use (
                            $school
                        ) {
                            $query->where(
                                'school_id',
                                $school->id
                            );
                        }
                    )
                    ->whereIn(
                        'status',
                        [
                            'submitted',
                            'late',
                            'graded',
                        ]
                    )
                    ->count();


            $gradedAssignments =
                $user
                    ->assignmentSubmissions()
                    ->whereHas(
                        'assignment',
                        function ($query) use (
                            $school
                        ) {
                            $query->where(
                                'school_id',
                                $school->id
                            );
                        }
                    )
                    ->where(
                        'status',
                        'graded'
                    )
                    ->count();


            $overdueAssignments = (
                clone $assignmentBase
            )
                ->whereNotNull(
                    'due_at'
                )
                ->where(
                    'due_at',
                    '<',
                    now()
                )
                ->whereDoesntHave(
                    'submissions',
                    function ($query) use (
                        $user
                    ) {
                        $query
                            ->where(
                                'student_id',
                                $user->id
                            )
                            ->whereIn(
                                'status',
                                [
                                    'submitted',
                                    'late',
                                    'graded',
                                ]
                            );
                    }
                )
                ->count();

        }

    @endphp


    <div class="learner-dashboard">


        {{-- ================================================================
            Header
        ================================================================= --}}

        <div class="page-header">

            <div>

                <span class="eyebrow">

                    {{
                        $isStudent
                            ? 'Learner Portal'
                            : 'Reader Portal'
                    }}

                </span>


                <h1>
                    Welcome, {{ $user->name }}
                </h1>


                <p>

                    @if ($school)

                        {{ $school->name }}

                        · Access your licensed literature,
                        assignments and learning activity.

                    @else

                        Access literature, continue reading
                        and monitor your learning activity.

                    @endif

                </p>

            </div>


            <div class="page-header__actions">

                @if ($isStudent)

                    <a
                        href="{{ route(
                            'school.library.index'
                        ) }}"
                        class="btn btn--primary"
                    >
                        Browse Library
                    </a>

                    <a
                        href="{{ route(
                            'student.assignments.index'
                        ) }}"
                        class="btn btn--secondary"
                    >
                        My Assignments
                    </a>

                @elseif (
                    Route::has(
                        'library.index'
                    )
                )

                    <a
                        href="{{ route(
                            'library.index'
                        ) }}"
                        class="btn btn--primary"
                    >
                        Browse Library
                    </a>

                @endif

            </div>

        </div>


        {{-- ================================================================
            Summary
        ================================================================= --}}

        <section class="learner-stats">

            <article class="learner-stat">

                <span>
                    Library Access
                </span>

                <strong>
                    Active
                </strong>

                <small>
                    Available literature resources
                </small>

            </article>


            @if ($isStudent)

                <article class="learner-stat">

                    <span>
                        Assignments
                    </span>

                    <strong>
                        {{ $totalAssignments }}
                    </strong>

                    <small>
                        Assigned learning tasks
                    </small>

                </article>


                <article class="learner-stat">

                    <span>
                        Submitted
                    </span>

                    <strong>
                        {{ $submittedAssignments }}
                    </strong>

                    <small>
                        Work submitted
                    </small>

                </article>


                <article class="learner-stat">

                    <span>
                        Graded
                    </span>

                    <strong>
                        {{ $gradedAssignments }}
                    </strong>

                    <small>
                        Results available
                    </small>

                </article>

            @else

                <article class="learner-stat">

                    <span>
                        Reading
                    </span>

                    <strong>
                        Ready
                    </strong>

                    <small>
                        Personal library access
                    </small>

                </article>

            @endif

        </section>


        {{-- ================================================================
            Overdue Warning
        ================================================================= --}}

        @if (
            $isStudent
            && $overdueAssignments > 0
        )

            <div class="assignment-warning">

                <div>

                    <strong>

                        {{ $overdueAssignments }}

                        overdue

                        {{
                            \Illuminate\Support\Str::plural(
                                'assignment',
                                $overdueAssignments
                            )
                        }}

                    </strong>

                    <p>
                        Late submissions are currently accepted
                        but will be marked as late.
                    </p>

                </div>


                <a
                    href="{{ route(
                        'student.assignments.index',
                        [
                            'status' =>
                                'overdue',
                        ]
                    ) }}"
                    class="btn btn--secondary"
                >
                    Review Overdue Work
                </a>

            </div>

        @endif


        {{-- ================================================================
            Main Workspace
        ================================================================= --}}

        <section class="learner-section">

            <div class="section-heading">

                <div>

                    <span class="eyebrow">
                        Learning Workspace
                    </span>

                    <h2>
                        My Literature
                    </h2>

                </div>

            </div>


            <div class="learner-grid">


                {{-- Library --}}

                <article class="learner-card">

                    <div class="learner-card__icon">
                        L
                    </div>


                    <div class="learner-card__body">

                        <h3>
                            My Library
                        </h3>

                        <p>

                            @if ($school)

                                Browse literature licensed
                                to your school and available
                                under your class and access rights.

                            @else

                                Browse literature available
                                through your account.

                            @endif

                        </p>

                    </div>


                    @if ($isStudent)

                        <a
                            href="{{ route(
                                'school.library.index'
                            ) }}"
                            class="learner-card__link"
                        >
                            Browse Library

                            <span>
                                →
                            </span>
                        </a>

                    @elseif (
                        Route::has(
                            'library.index'
                        )
                    )

                        <a
                            href="{{ route(
                                'library.index'
                            ) }}"
                            class="learner-card__link"
                        >
                            Browse Library

                            <span>
                                →
                            </span>
                        </a>

                    @endif

                </article>


                {{-- Assignments --}}

                @if ($isStudent)

                    <article class="learner-card">

                        <div class="learner-card__icon">
                            A
                        </div>


                        <div class="learner-card__body">

                            <h3>
                                Assignments
                            </h3>

                            <p>
                                View assigned reading tasks,
                                save drafts, submit work and
                                review teacher feedback.
                            </p>

                        </div>


                        <a
                            href="{{ route(
                                'student.assignments.index'
                            ) }}"
                            class="learner-card__link"
                        >
                            View Assignments

                            <span>
                                →
                            </span>
                        </a>

                    </article>

                @endif


                {{-- Submitted Work --}}

                @if ($isStudent)

                    <article class="learner-card">

                        <div class="learner-card__icon">
                            S
                        </div>


                        <div class="learner-card__body">

                            <h3>
                                Submitted Work
                            </h3>

                            <p>
                                Review assignments you have
                                already submitted and monitor
                                grading status.
                            </p>

                        </div>


                        <a
                            href="{{ route(
                                'student.assignments.index',
                                [
                                    'status' =>
                                        'submitted',
                                ]
                            ) }}"
                            class="learner-card__link"
                        >
                            View Submitted Work

                            <span>
                                →
                            </span>
                        </a>

                    </article>

                @endif


                {{-- Results --}}

                @if ($isStudent)

                    <article class="learner-card">

                        <div class="learner-card__icon">
                            G
                        </div>


                        <div class="learner-card__body">

                            <h3>
                                Results & Feedback
                            </h3>

                            <p>
                                Review graded assignments,
                                marks and feedback provided
                                by your teachers.
                            </p>

                        </div>


                        <a
                            href="{{ route(
                                'student.assignments.index',
                                [
                                    'status' =>
                                        'graded',
                                ]
                            ) }}"
                            class="learner-card__link"
                        >
                            View Results

                            <span>
                                →
                            </span>
                        </a>

                    </article>

                @endif


                {{-- Continue Reading --}}

                <article class="learner-card">

                    <div class="learner-card__icon">
                        R
                    </div>


                    <div class="learner-card__body">

                        <h3>
                            Continue Reading
                        </h3>

                        <p>
                            Resume books from your most
                            recently saved reading position.
                        </p>

                    </div>


                    @if (
                        Route::has(
                            'student.reading.index'
                        )
                    )

                        <a
                            href="{{ route(
                                'student.reading.index'
                            ) }}"
                            class="learner-card__link"
                        >
                            Continue Reading

                            <span>
                                →
                            </span>
                        </a>

                    @else

                        <span class="learner-card__disabled">
                            Reading history coming next
                        </span>

                    @endif

                </article>


                {{-- Bookmarks --}}

                <article class="learner-card">

                    <div class="learner-card__icon">
                        M
                    </div>


                    <div class="learner-card__body">

                        <h3>
                            Bookmarks
                        </h3>

                        <p>
                            Return to pages and sections
                            saved while reading.
                        </p>

                    </div>


                    @if (
                        Route::has(
                            'student.bookmarks.index'
                        )
                    )

                        <a
                            href="{{ route(
                                'student.bookmarks.index'
                            ) }}"
                            class="learner-card__link"
                        >
                            View Bookmarks

                            <span>
                                →
                            </span>
                        </a>

                    @else

                        <span class="learner-card__disabled">
                            Saved inside each book
                        </span>

                    @endif

                </article>


                {{-- Access Requests --}}

                @if ($isStudent)

                    <article class="learner-card">

                        <div class="learner-card__icon">
                            Q
                        </div>


                        <div class="learner-card__body">

                            <h3>
                                Access Requests
                            </h3>

                            <p>
                                Track requests for licensed
                                books outside your normal
                                class access.
                            </p>

                        </div>


                        @if (
                            Route::has(
                                'school.library.requests.index'
                            )
                        )

                            <a
                                href="{{ route(
                                    'school.library.requests.index'
                                ) }}"
                                class="learner-card__link"
                            >
                                View Requests

                                <span>
                                    →
                                </span>
                            </a>

                        @else

                            <span class="learner-card__disabled">
                                Request tracking unavailable
                            </span>

                        @endif

                    </article>

                @endif


                {{-- Profile --}}

                <article class="learner-card">

                    <div class="learner-card__icon">
                        U
                    </div>


                    <div class="learner-card__body">

                        <h3>
                            Profile
                        </h3>

                        <p>
                            Review your personal account
                            and education information.
                        </p>

                    </div>


                    @if (
                        Route::has(
                            'profile.edit'
                        )
                    )

                        <a
                            href="{{ route(
                                'profile.edit'
                            ) }}"
                            class="learner-card__link"
                        >
                            Manage Profile

                            <span>
                                →
                            </span>
                        </a>

                    @else

                        <span class="learner-card__disabled">
                            Account settings
                        </span>

                    @endif

                </article>

            </div>

        </section>


        {{-- ================================================================
            Student Workflow
        ================================================================= --}}

        @if ($isStudent)

            <section class="learner-section">

                <div class="section-heading">

                    <div>

                        <span class="eyebrow">
                            Student Journey
                        </span>

                        <h2>
                            How your learning works
                        </h2>

                    </div>

                </div>


                <div class="learner-workflow">


                    <div class="workflow-step">

                        <span>
                            1
                        </span>

                        <div>

                            <strong>
                                Open your library
                            </strong>

                            <p>
                                Browse books licensed to your
                                school and available to your class.
                            </p>

                        </div>

                    </div>


                    <div class="workflow-step">

                        <span>
                            2
                        </span>

                        <div>

                            <strong>
                                Read assigned material
                            </strong>

                            <p>
                                Use the protected reader to
                                complete the required pages.
                            </p>

                        </div>

                    </div>


                    <div class="workflow-step">

                        <span>
                            3
                        </span>

                        <div>

                            <strong>
                                Complete your work
                            </strong>

                            <p>
                                Save drafts and submit your
                                assignment before the deadline.
                            </p>

                        </div>

                    </div>


                    <div class="workflow-step">

                        <span>
                            4
                        </span>

                        <div>

                            <strong>
                                Review feedback
                            </strong>

                            <p>
                                View your score and teacher
                                feedback after grading.
                            </p>

                        </div>

                    </div>

                </div>

            </section>

        @endif

    </div>


    <style>

        .learner-dashboard {
            display: grid;
            gap: 22px;
        }


        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
        }


        .page-header h1 {
            margin: 4px 0;
        }


        .page-header p {
            max-width: 680px;
            margin: 0;

            color:
                var(--color-text-muted);

            font-size: .62rem;
            line-height: 1.55;
        }


        .page-header__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }


        .learner-stats {
            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap: 10px;
        }


        .learner-stat {
            padding: 16px;

            border:
                1px solid
                var(--color-border);

            border-radius:
                var(--radius-lg);

            background:
                var(--color-surface);
        }


        .learner-stat > span {
            display: block;

            color:
                var(--color-text-muted);

            font-size: .51rem;
            font-weight: 750;

            text-transform: uppercase;
            letter-spacing: .05em;
        }


        .learner-stat strong {
            display: block;

            margin: 6px 0 3px;

            color:
                var(--color-text);

            font-size: 1.3rem;
        }


        .learner-stat small {
            color:
                var(--color-text-muted);

            font-size: .5rem;
        }


        .assignment-warning {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;

            padding: 14px 16px;

            border:
                1px solid
                #fecdca;

            background:
                #fff8f7;
        }


        .assignment-warning strong {
            color: #b42318;
        }


        .assignment-warning p {
            margin: 3px 0 0;

            color: #912018;

            font-size: .54rem;
        }


        .learner-section {
            display: grid;
            gap: 12px;
        }


        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }


        .section-heading h2 {
            margin: 3px 0 0;
            font-size: .88rem;
        }


        .learner-grid {
            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap: 12px;
        }


        .learner-card {
            min-height: 190px;

            display: flex;
            flex-direction: column;

            padding: 15px;

            border:
                1px solid
                var(--color-border);

            border-radius:
                var(--radius-lg);

            background:
                var(--color-surface);

            transition:
                border-color .15s ease,
                transform .15s ease;
        }


        .learner-card:hover {
            border-color:
                var(--brand-300);

            transform:
                translateY(-1px);
        }


        .learner-card__icon {
            width: 32px;
            height: 32px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius:
                var(--radius-md);

            background:
                var(--color-surface-soft);

            color:
                var(--color-primary);

            font-size: .68rem;
            font-weight: 850;
        }


        .learner-card__body {
            flex: 1;
        }


        .learner-card h3 {
            margin: 13px 0 4px;
            font-size: .74rem;
        }


        .learner-card p {
            margin: 0;

            color:
                var(--color-text-muted);

            font-size: .55rem;
            line-height: 1.6;
        }


        .learner-card__link {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-top: 16px;
            padding-top: 10px;

            border-top:
                1px solid
                var(--color-border);

            color:
                var(--color-primary);

            font-size: .54rem;
            font-weight: 750;

            text-decoration: none;
        }


        .learner-card__disabled {
            margin-top: 16px;
            padding-top: 10px;

            border-top:
                1px solid
                var(--color-border);

            color:
                var(--color-text-muted);

            font-size: .51rem;
            font-weight: 650;
        }


        .learner-workflow {
            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap: 10px;
        }


        .workflow-step {
            display: flex;
            align-items: flex-start;
            gap: 10px;

            padding: 14px;

            border:
                1px solid
                var(--color-border);

            border-radius:
                var(--radius-lg);

            background:
                var(--color-surface);
        }


        .workflow-step > span {
            width: 25px;
            height: 25px;

            flex:
                0 0
                25px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background:
                var(--color-primary);

            color: white;

            font-size: .52rem;
            font-weight: 800;
        }


        .workflow-step strong {
            display: block;

            color:
                var(--color-text);

            font-size: .58rem;
        }


        .workflow-step p {
            margin: 3px 0 0;

            color:
                var(--color-text-muted);

            font-size: .51rem;
            line-height: 1.5;
        }


        @media (
            max-width: 1000px
        ) {

            .learner-stats,
            .learner-workflow {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(
                            0,
                            1fr
                        )
                    );
            }


            .learner-grid {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(
                            0,
                            1fr
                        )
                    );
            }

        }


        @media (
            max-width: 650px
        ) {

            .page-header {
                flex-direction:
                    column;
            }


            .assignment-warning {
                flex-direction:
                    column;

                align-items:
                    flex-start;
            }


            .learner-stats,
            .learner-grid,
            .learner-workflow {
                grid-template-columns:
                    1fr;
            }

        }

    </style>

</x-layouts.dashboard>