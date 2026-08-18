<x-layouts.dashboard title="Teacher Dashboard — LiteraHub">

    @php
        $user = auth()->user();

        $school = $user
            ->schools()
            ->wherePivot('status', 'active')
            ->first();

        $classes = method_exists($user, 'teacherClasses')
            ? $user->teacherClasses()->count()
            : 0;
    @endphp


    <div class="teacher-dashboard">


        {{-- ================================================================
             HEADER
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
                    href="{{ route('school.library.index') }}"
                    class="btn btn--secondary"
                >
                    Browse Library
                </a>


                @if (Route::has('teacher.assignments.create'))

                    <a
                        href="{{ route(
                            'teacher.assignments.create'
                        ) }}"
                        class="btn btn--primary"
                    >
                        Create Assignment
                    </a>

                @endif

            </div>

        </div>



        {{-- ================================================================
             QUICK SUMMARY
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
                    Library
                </span>

                <strong>
                    Ready
                </strong>

                <small>
                    Licensed institutional books
                </small>

            </article>


            <article class="teacher-stat">

                <span>
                    Assignments
                </span>

                <strong>
                    —

                </strong>

                <small>
                    Published and active
                </small>

            </article>


            <article class="teacher-stat">

                <span>
                    Pending Reviews
                </span>

                <strong>
                    —
                </strong>

                <small>
                    Student submissions
                </small>

            </article>

        </section>



        {{-- ================================================================
             PRIMARY WORKFLOWS
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
                            View the classes and learner groups
                            assigned to you.
                        </p>

                    </div>


                    @if (Route::has('teacher.classes.index'))

                        <a
                            href="{{ route(
                                'teacher.classes.index'
                            ) }}"
                            class="teacher-card__link"
                        >
                            View Classes
                            <span>→</span>
                        </a>

                    @else

                        <span class="teacher-card__disabled">
                            Coming next
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
                            Browse books licensed to your institution
                            and available for teaching.
                        </p>

                    </div>


                    <a
                        href="{{ route(
                            'school.library.index'
                        ) }}"
                        class="teacher-card__link"
                    >
                        Browse Library
                        <span>→</span>
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
                            Create literature assignments,
                            reading tasks and assessments.
                        </p>

                    </div>


                    @if (Route::has('teacher.assignments.index'))

                        <a
                            href="{{ route(
                                'teacher.assignments.index'
                            ) }}"
                            class="teacher-card__link"
                        >
                            Manage Assignments
                            <span>→</span>
                        </a>

                    @else

                        <span class="teacher-card__disabled">
                            Coming next
                        </span>

                    @endif

                </article>



                {{-- Reading Lists --}}

                <article class="teacher-card">

                    <div class="teacher-card__icon">
                        R
                    </div>

                    <div class="teacher-card__body">

                        <h3>
                            Reading Lists
                        </h3>

                        <p>
                            Organise licensed books into structured
                            reading lists for your classes.
                        </p>

                    </div>


                    @if (Route::has('teacher.reading-lists.index'))

                        <a
                            href="{{ route(
                                'teacher.reading-lists.index'
                            ) }}"
                            class="teacher-card__link"
                        >
                            Reading Lists
                            <span>→</span>
                        </a>

                    @else

                        <span class="teacher-card__disabled">
                            Later MVP
                        </span>

                    @endif

                </article>



                {{-- Students --}}

                <article class="teacher-card">

                    <div class="teacher-card__icon">
                        S
                    </div>

                    <div class="teacher-card__body">

                        <h3>
                            Students
                        </h3>

                        <p>
                            Review learners, reading activity and
                            assignment participation.
                        </p>

                    </div>


                    @if (Route::has('teacher.students.index'))

                        <a
                            href="{{ route(
                                'teacher.students.index'
                            ) }}"
                            class="teacher-card__link"
                        >
                            View Students
                            <span>→</span>
                        </a>

                    @else

                        <span class="teacher-card__disabled">
                            Coming soon
                        </span>

                    @endif

                </article>



                {{-- Performance --}}

                <article class="teacher-card">

                    <div class="teacher-card__icon">
                        P
                    </div>

                    <div class="teacher-card__body">

                        <h3>
                            Performance
                        </h3>

                        <p>
                            Review assignment results and
                            learner reading progress.
                        </p>

                    </div>


                    @if (Route::has('teacher.performance.index'))

                        <a
                            href="{{ route(
                                'teacher.performance.index'
                            ) }}"
                            class="teacher-card__link"
                        >
                            View Performance
                            <span>→</span>
                        </a>

                    @else

                        <span class="teacher-card__disabled">
                            Later MVP
                        </span>

                    @endif

                </article>


            </div>

        </section>



        {{-- ================================================================
             NEXT ACTIONS
        ================================================================= --}}

        <section class="teacher-section">

            <div class="section-heading">

                <div>

                    <span class="eyebrow">
                        Workflow
                    </span>

                    <h2>
                        Start Teaching
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
                            Browse the library
                        </strong>

                        <p>
                            Find a book licensed to your school.
                        </p>

                    </div>

                </div>


                <div class="workflow-step">

                    <span>
                        2
                    </span>

                    <div>

                        <strong>
                            Select a class
                        </strong>

                        <p>
                            Choose which learners should receive
                            the reading task.
                        </p>

                    </div>

                </div>


                <div class="workflow-step">

                    <span>
                        3
                    </span>

                    <div>

                        <strong>
                            Create an assignment
                        </strong>

                        <p>
                            Add instructions, reading pages and
                            a submission deadline.
                        </p>

                    </div>

                </div>


                <div class="workflow-step">

                    <span>
                        4
                    </span>

                    <div>

                        <strong>
                            Review submissions
                        </strong>

                        <p>
                            Grade student work and provide feedback.
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


        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
        }


        .page-header h1 {
            margin:
                4px 0;
        }


        .page-header p {
            margin: 0;
            color: var(--color-text-muted);
            font-size: .62rem;
        }


        .page-header__actions {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
        }



        /*
        |--------------------------------------------------------------------------
        | Stats
        |--------------------------------------------------------------------------
        */

        .teacher-stats {
            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(0, 1fr)
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

            font-size:
                .52rem;

            font-weight:
                700;

            text-transform:
                uppercase;

            letter-spacing:
                .05em;
        }


        .teacher-stat strong {
            display: block;

            margin:
                6px 0 3px;

            color:
                var(--color-text);

            font-size:
                1.35rem;
        }


        .teacher-stat small {
            color:
                var(--color-text-muted);

            font-size:
                .51rem;
        }



        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

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
            margin:
                3px 0 0;

            font-size:
                .88rem;
        }



        /*
        |--------------------------------------------------------------------------
        | Cards
        |--------------------------------------------------------------------------
        */

        .teacher-grid {
            display: grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(0, 1fr)
                );

            gap: 12px;
        }


        .teacher-card {
            display: flex;
            flex-direction: column;

            min-height:
                190px;

            padding:
                15px;

            border:
                1px solid
                var(--color-border);

            border-radius:
                var(--radius-lg);

            background:
                var(--color-surface);

            transition:
                border-color
                .15s ease,
                transform
                .15s ease;
        }


        .teacher-card:hover {
            border-color:
                var(--brand-300);

            transform:
                translateY(-1px);
        }


        .teacher-card__icon {
            width:
                32px;

            height:
                32px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                var(--radius-md);

            background:
                var(--color-surface-soft);

            color:
                var(--color-primary);

            font-size:
                .68rem;

            font-weight:
                850;
        }


        .teacher-card__body {
            flex: 1;
        }


        .teacher-card h3 {
            margin:
                13px 0 4px;

            font-size:
                .74rem;
        }


        .teacher-card p {
            margin: 0;

            color:
                var(--color-text-muted);

            font-size:
                .55rem;

            line-height:
                1.6;
        }


        .teacher-card__link {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-top:
                16px;

            padding-top:
                10px;

            border-top:
                1px solid
                var(--color-border);

            color:
                var(--color-primary);

            font-size:
                .54rem;

            font-weight:
                750;

            text-decoration:
                none;
        }


        .teacher-card__disabled {
            margin-top:
                16px;

            padding-top:
                10px;

            border-top:
                1px solid
                var(--color-border);

            color:
                var(--color-text-muted);

            font-size:
                .51rem;

            font-weight:
                650;
        }



        /*
        |--------------------------------------------------------------------------
        | Workflow
        |--------------------------------------------------------------------------
        */

        .teacher-workflow {
            display: grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(0, 1fr)
                );

            gap: 10px;
        }


        .workflow-step {
            display: flex;
            align-items: flex-start;
            gap: 10px;

            padding:
                14px;

            border:
                1px solid
                var(--color-border);

            border-radius:
                var(--radius-lg);

            background:
                var(--color-surface);
        }


        .workflow-step > span {
            width:
                25px;

            height:
                25px;

            flex:
                0 0
                25px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                50%;

            background:
                var(--color-primary);

            color:
                white;

            font-size:
                .52rem;

            font-weight:
                800;
        }


        .workflow-step strong {
            display: block;

            color:
                var(--color-text);

            font-size:
                .58rem;
        }


        .workflow-step p {
            margin:
                3px 0 0;

            color:
                var(--color-text-muted);

            font-size:
                .51rem;

            line-height:
                1.5;
        }



        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (
            max-width: 1000px
        ) {

            .teacher-stats,
            .teacher-workflow {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
                    );
            }


            .teacher-grid {
                grid-template-columns:
                    repeat(
                        2,
                        minmax(0, 1fr)
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