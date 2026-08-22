<x-layouts.dashboard title="Teacher Dashboard — LiteraHub">

    @php

        $user = auth()->user();

        $school = $user
            ->activeSchools()
            ->first();


        $classes = $school
            ? $user
                ->teacherClasses()
                ->where(
                    'school_classes.school_id',
                    $school->id
                )
                ->count()
            : 0;


        $assignmentQuery = $school
            ? \App\Models\Assignment::query()
                ->where(
                    'school_id',
                    $school->id
                )
                ->where(
                    'creator_id',
                    $user->id
                )
            : null;


        $assignments = $assignmentQuery
            ? (clone $assignmentQuery)
                ->whereIn(
                    'status',
                    [
                        'draft',
                        'published',
                        'closed',
                    ]
                )
                ->count()
            : 0;


        $publishedAssignments = $assignmentQuery
            ? (clone $assignmentQuery)
                ->where(
                    'status',
                    'published'
                )
                ->count()
            : 0;


        $pendingReviews = $school
            ? \App\Models\AssignmentSubmission::query()
                ->whereHas(
                    'assignment',
                    function ($query) use (
                        $school,
                        $user
                    ) {
                        $query
                            ->where(
                                'school_id',
                                $school->id
                            )
                            ->where(
                                'creator_id',
                                $user->id
                            );
                    }
                )
                ->whereIn(
                    'status',
                    [
                        'submitted',
                        'late',
                    ]
                )
                ->count()
            : 0;

    @endphp


    <div class="teacher-dashboard">


        {{-- ================================================================
            Header
        ================================================================= --}}

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    Teacher Portal
                </span>

                <h1>
                    Welcome, {{ $user->name }}
                </h1>

                <p>

                    @if ($school)

                        {{ $school->name }}

                    @else

                        Your teaching workspace

                    @endif

                </p>

            </div>


            <div class="page-header__actions">

                <a
                    href="{{ route(
                        'school.library.index'
                    ) }}"
                    class="btn btn--secondary"
                >
                    Browse Library
                </a>


                <a
                    href="{{ route(
                        'school.assignments.create'
                    ) }}"
                    class="btn btn--primary"
                >
                    Create Assignment
                </a>

            </div>

        </div>


        {{-- ================================================================
            Summary
        ================================================================= --}}

        <section class="teacher-stats">


            <article class="teacher-stat">

                <span>
                    My Classes
                </span>

                <strong>
                    {{ $classes }}
                </strong>

                <small>
                    Active teaching groups
                </small>

            </article>


            <article class="teacher-stat">

                <span>
                    Assignments
                </span>

                <strong>
                    {{ $assignments }}
                </strong>

                <small>
                    Assignments created by you
                </small>

            </article>


            <article class="teacher-stat">

                <span>
                    Published
                </span>

                <strong>
                    {{ $publishedAssignments }}
                </strong>

                <small>
                    Active learner tasks
                </small>

            </article>


            <article class="teacher-stat">

                <span>
                    Pending Reviews
                </span>

                <strong>
                    {{ $pendingReviews }}
                </strong>

                <small>
                    Student work awaiting grading
                </small>

            </article>

        </section>


        {{-- ================================================================
            Academic Workspace
        ================================================================= --}}

        <section class="teacher-section">

            <div class="section-heading">

                <div>

                    <span class="eyebrow">
                        Teaching Tools
                    </span>

                    <h2>
                        Academic Workspace
                    </h2>

                </div>

            </div>


            <div class="teacher-grid">


                {{-- Classes --}}

                <article class="teacher-card">

                    <div class="teacher-card__icon">
                        C
                    </div>

                    <div class="teacher-card__body">

                        <h3>
                            My Classes
                        </h3>

                        <p>
                            View the classes and learner
                            groups assigned to you.
                        </p>

                    </div>


                    @if (
                        Route::has(
                            'teacher.classes.index'
                        )
                    )

                        <a
                            href="{{ route(
                                'teacher.classes.index'
                            ) }}"
                            class="teacher-card__link"
                        >
                            View Classes

                            <span>
                                →
                            </span>
                        </a>

                    @else

                        <span class="teacher-card__disabled">
                            Class view coming next
                        </span>

                    @endif

                </article>


                {{-- Library --}}

                <article class="teacher-card">

                    <div class="teacher-card__icon">
                        L
                    </div>

                    <div class="teacher-card__body">

                        <h3>
                            Digital Library
                        </h3>

                        <p>
                            Browse books actively licensed
                            to your institution and available
                            for teaching.
                        </p>

                    </div>


                    <a
                        href="{{ route(
                            'school.library.index'
                        ) }}"
                        class="teacher-card__link"
                    >
                        Browse Library

                        <span>
                            →
                        </span>
                    </a>

                </article>


                {{-- Assignments --}}

                <article class="teacher-card">

                    <div class="teacher-card__icon">
                        A
                    </div>

                    <div class="teacher-card__body">

                        <h3>
                            Assignments
                        </h3>

                        <p>
                            Create reading tasks using
                            licensed books, reading ranges,
                            deadlines and marks.
                        </p>

                    </div>


                    <a
                        href="{{ route(
                            'school.assignments.index'
                        ) }}"
                        class="teacher-card__link"
                    >
                        Manage Assignments

                        <span>
                            →
                        </span>
                    </a>

                </article>


                {{-- New Assignment --}}

                <article class="teacher-card">

                    <div class="teacher-card__icon">
                        +
                    </div>

                    <div class="teacher-card__body">

                        <h3>
                            New Assignment
                        </h3>

                        <p>
                            Assign a licensed book to one
                            of your teaching classes.
                        </p>

                    </div>


                    <a
                        href="{{ route(
                            'school.assignments.create'
                        ) }}"
                        class="teacher-card__link"
                    >
                        Create Assignment

                        <span>
                            →
                        </span>
                    </a>

                </article>


                {{-- Submissions --}}

                <article class="teacher-card">

                    <div class="teacher-card__icon">
                        S
                    </div>

                    <div class="teacher-card__body">

                        <h3>
                            Student Submissions
                        </h3>

                        <p>
                            Review submitted learner work,
                            late submissions, scores and feedback.
                        </p>

                    </div>


                    @if (
                        Route::has(
                            'teacher.submissions.index'
                        )
                    )

                        <a
                            href="{{ route(
                                'teacher.submissions.index'
                            ) }}"
                            class="teacher-card__link"
                        >
                            Review Submissions

                            <span>
                                →
                            </span>
                        </a>

                    @else

                        <a
                            href="{{ route(
                                'school.assignments.index'
                            ) }}"
                            class="teacher-card__link"
                        >
                            Open Assignments

                            <span>
                                →
                            </span>
                        </a>

                    @endif

                </article>


                {{-- Students --}}

                <article class="teacher-card">

                    <div class="teacher-card__icon">
                        U
                    </div>

                    <div class="teacher-card__body">

                        <h3>
                            Students
                        </h3>

                        <p>
                            Review learners in your classes,
                            their assignment participation
                            and reading activity.
                        </p>

                    </div>


                    @if (
                        Route::has(
                            'teacher.students.index'
                        )
                    )

                        <a
                            href="{{ route(
                                'teacher.students.index'
                            ) }}"
                            class="teacher-card__link"
                        >
                            View Students

                            <span>
                                →
                            </span>
                        </a>

                    @else

                        <span class="teacher-card__disabled">
                            Student overview coming next
                        </span>

                    @endif

                </article>

            </div>

        </section>


        {{-- ================================================================
            Workflow
        ================================================================= --}}

        <section class="teacher-section">

            <div class="section-heading">

                <div>

                    <span class="eyebrow">
                        Workflow
                    </span>

                    <h2>
                        Assignment Cycle
                    </h2>

                </div>

            </div>


            <div class="teacher-workflow">


                <div class="workflow-step">

                    <span>
                        1
                    </span>

                    <div>

                        <strong>
                            Choose a licensed book
                        </strong>

                        <p>
                            Browse resources permitted for
                            teacher assignment.
                        </p>

                    </div>

                </div>


                <div class="workflow-step">

                    <span>
                        2
                    </span>

                    <div>

                        <strong>
                            Select your class
                        </strong>

                        <p>
                            Assign the work only to classes
                            you currently teach.
                        </p>

                    </div>

                </div>


                <div class="workflow-step">

                    <span>
                        3
                    </span>

                    <div>

                        <strong>
                            Publish assignment
                        </strong>

                        <p>
                            Add reading pages, instructions,
                            marks and the deadline.
                        </p>

                    </div>

                </div>


                <div class="workflow-step">

                    <span>
                        4
                    </span>

                    <div>

                        <strong>
                            Grade submissions
                        </strong>

                        <p>
                            Review student work, award marks
                            and provide feedback.
                        </p>

                    </div>

                </div>

            </div>

        </section>

    </div>


    <style>

        .teacher-dashboard {
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
            margin: 0;

            color:
                var(--color-text-muted);

            font-size: .62rem;
        }


        .page-header__actions {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
        }


        .teacher-stats {
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


        .teacher-stat {
            padding: 16px;

            border:
                1px solid
                var(--color-border);

            border-radius:
                var(--radius-lg);

            background:
                var(--color-surface);
        }


        .teacher-stat span {
            display: block;

            color:
                var(--color-text-muted);

            font-size: .52rem;
            font-weight: 700;

            text-transform: uppercase;
            letter-spacing: .05em;
        }


        .teacher-stat strong {
            display: block;

            margin: 6px 0 3px;

            color:
                var(--color-text);

            font-size: 1.35rem;
        }


        .teacher-stat small {
            color:
                var(--color-text-muted);

            font-size: .51rem;
        }


        .teacher-section {
            display: grid;
            gap: 12px;
        }


        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }


        .section-heading h2 {
            margin: 3px 0 0;
            font-size: .88rem;
        }


        .teacher-grid {
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


        .teacher-card {
            display: flex;
            flex-direction: column;

            min-height: 190px;
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


        .teacher-card:hover {
            border-color:
                var(--brand-300);

            transform:
                translateY(-1px);
        }


        .teacher-card__icon {
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


        .teacher-card__body {
            flex: 1;
        }


        .teacher-card h3 {
            margin: 13px 0 4px;
            font-size: .74rem;
        }


        .teacher-card p {
            margin: 0;

            color:
                var(--color-text-muted);

            font-size: .55rem;
            line-height: 1.6;
        }


        .teacher-card__link {
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


        .teacher-card__disabled {
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


        .teacher-workflow {
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

            .teacher-stats,
            .teacher-workflow {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(
                            0,
                            1fr
                        )
                    );
            }


            .teacher-grid {
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


            .teacher-stats,
            .teacher-grid,
            .teacher-workflow {
                grid-template-columns:
                    1fr;
            }

        }

    </style>

</x-layouts.dashboard>