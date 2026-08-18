<x-layouts.dashboard title="My Library — LiteraHub">

    @php
        $user = auth()->user();

        $school = method_exists($user, 'schools')
            ? $user
                ->schools()
                ->wherePivot('status', 'active')
                ->first()
            : null;

        $isStudent =
            $user->hasRole('student');

        $isIndividual =
            $user->hasRole('individual_subscriber');
    @endphp


    <div class="learner-dashboard">


        {{-- ================================================================
             HEADER
        ================================================================= --}}

        <div class="page-header">

            <div>

                <span class="eyebrow">
                    {{ $isStudent
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
                        · Access your assigned and approved
                        literature resources.

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

                @elseif (Route::has('library.index'))

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
             SUMMARY
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


            <article class="learner-stat">

                <span>
                    Borrowed Books
                </span>

                <strong>
                    —
                </strong>

                <small>
                    Currently active loans
                </small>

            </article>


            <article class="learner-stat">

                <span>
                    Assignments
                </span>

                <strong>
                    —
                </strong>

                <small>
                    Active learning tasks
                </small>

            </article>


            <article class="learner-stat">

                <span>
                    Saved Pages
                </span>

                <strong>
                    —
                </strong>

                <small>
                    Bookmarks across your books
                </small>

            </article>

        </section>



        {{-- ================================================================
             MAIN FEATURES
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


                {{-- ========================================================
                     LIBRARY
                ========================================================= --}}

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

                                Browse literature licensed to your
                                school and available under your
                                current access rights.

                            @else

                                Browse literature available through
                                your current account.

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

                    @elseif (Route::has('library.index'))

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

                    @else

                        <span class="learner-card__disabled">
                            Library unavailable
                        </span>

                    @endif

                </article>



                {{-- ========================================================
                     CONTINUE READING
                ========================================================= --}}

                <article class="learner-card">

                    <div class="learner-card__icon">
                        R
                    </div>


                    <div class="learner-card__body">

                        <h3>
                            Continue Reading
                        </h3>

                        <p>
                            Resume your books from the most recently
                            saved reading position.
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



                {{-- ========================================================
                     BORROWED BOOKS
                ========================================================= --}}

                @if ($isStudent)

                    <article class="learner-card">

                        <div class="learner-card__icon">
                            B
                        </div>


                        <div class="learner-card__body">

                            <h3>
                                Borrowed Books
                            </h3>

                            <p>
                                Review your active digital loans,
                                return books and monitor due dates.
                            </p>

                        </div>


                        @if (
                            Route::has(
                                'student.borrowings.index'
                            )
                        )

                            <a
                                href="{{ route(
                                    'student.borrowings.index'
                                ) }}"
                                class="learner-card__link"
                            >
                                View Borrowed Books

                                <span>
                                    →
                                </span>
                            </a>

                        @else

                            <span class="learner-card__disabled">
                                Available from individual books
                            </span>

                        @endif

                    </article>

                @endif



                {{-- ========================================================
                     BOOKMARKS
                ========================================================= --}}

                <article class="learner-card">

                    <div class="learner-card__icon">
                        M
                    </div>


                    <div class="learner-card__body">

                        <h3>
                            Bookmarks
                        </h3>

                        <p>
                            Return to pages and sections you have
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



                {{-- ========================================================
                     ASSIGNMENTS
                ========================================================= --}}

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
                            assessments and upcoming deadlines.
                        </p>

                    </div>


                    @if (
                        Route::has(
                            'student.assignments.index'
                        )
                    )

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

                    @else

                        <span class="learner-card__disabled">
                            Coming next
                        </span>

                    @endif

                </article>



                {{-- ========================================================
                     ACCESS REQUESTS
                ========================================================= --}}

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
                                Track requests for school-licensed
                                books outside your current class
                                assignments.
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
                                No request page available
                            </span>

                        @endif

                    </article>

                @endif



                {{-- ========================================================
                     READING PROGRESS
                ========================================================= --}}

                <article class="learner-card">

                    <div class="learner-card__icon">
                        P
                    </div>


                    <div class="learner-card__body">

                        <h3>
                            Reading Progress
                        </h3>

                        <p>
                            Review your reading activity,
                            books completed and learning progress.
                        </p>

                    </div>


                    @if (
                        Route::has(
                            'student.progress.index'
                        )
                    )

                        <a
                            href="{{ route(
                                'student.progress.index'
                            ) }}"
                            class="learner-card__link"
                        >
                            View Progress

                            <span>
                                →
                            </span>
                        </a>

                    @else

                        <span class="learner-card__disabled">
                            Later MVP
                        </span>

                    @endif

                </article>



                {{-- ========================================================
                     SUBSCRIPTION
                ========================================================= --}}

                @if ($isIndividual)

                    <article class="learner-card">

                        <div class="learner-card__icon">
                            S
                        </div>


                        <div class="learner-card__body">

                            <h3>
                                Subscription
                            </h3>

                            <p>
                                Review your plan, account access
                                and renewal information.
                            </p>

                        </div>


                        @if (
                            Route::has(
                                'subscriptions.show'
                            )
                        )

                            <a
                                href="{{ route(
                                    'subscriptions.show'
                                ) }}"
                                class="learner-card__link"
                            >
                                Manage Subscription

                                <span>
                                    →
                                </span>
                            </a>

                        @else

                            <span class="learner-card__disabled">
                                Subscription management later
                            </span>

                        @endif

                    </article>

                @endif



                {{-- ========================================================
                     PROFILE
                ========================================================= --}}

                <article class="learner-card">

                    <div class="learner-card__icon">
                        U
                    </div>


                    <div class="learner-card__body">

                        <h3>
                            Profile
                        </h3>

                        <p>
                            Review your personal account and
                            education information.
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
             STUDENT FLOW
        ================================================================= --}}

        @if ($isStudent)

            <section class="learner-section">

                <div class="section-heading">

                    <div>

                        <span class="eyebrow">
                            Student Journey
                        </span>

                        <h2>
                            How your library works
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
                                Browse books licensed to your school
                                and available to your class.
                            </p>

                        </div>

                    </div>


                    <div class="workflow-step">

                        <span>
                            2
                        </span>

                        <div>

                            <strong>
                                Borrow when required
                            </strong>

                            <p>
                                Start a digital loan where the
                                book's rights require borrowing.
                            </p>

                        </div>

                    </div>


                    <div class="workflow-step">

                        <span>
                            3
                        </span>

                        <div>

                            <strong>
                                Read and bookmark
                            </strong>

                            <p>
                                Use the protected reader and save
                                important pages as you study.
                            </p>

                        </div>

                    </div>


                    <div class="workflow-step">

                        <span>
                            4
                        </span>

                        <div>

                            <strong>
                                Complete assignments
                            </strong>

                            <p>
                                Respond to reading tasks issued by
                                your teachers.
                            </p>

                        </div>

                    </div>


                </div>

            </section>

        @endif


    </div>



    <style>

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        .learner-dashboard {
            display:
                grid;

            gap:
                22px;
        }


        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .page-header {
            display:
                flex;

            align-items:
                flex-start;

            justify-content:
                space-between;

            gap:
                20px;
        }


        .page-header h1 {
            margin:
                4px 0;
        }


        .page-header p {
            max-width:
                680px;

            margin:
                0;

            color:
                var(--color-text-muted);

            font-size:
                .62rem;

            line-height:
                1.55;
        }


        .page-header__actions {
            display:
                flex;

            flex-wrap:
                wrap;

            gap:
                7px;
        }


        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        .learner-stats {
            display:
                grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap:
                10px;
        }


        .learner-stat {
            padding:
                16px;

            border:
                1px solid
                var(--color-border);

            border-radius:
                var(--radius-lg);

            background:
                var(--color-surface);
        }


        .learner-stat > span {
            display:
                block;

            color:
                var(--color-text-muted);

            font-size:
                .51rem;

            font-weight:
                750;

            text-transform:
                uppercase;

            letter-spacing:
                .05em;
        }


        .learner-stat strong {
            display:
                block;

            margin:
                6px 0 3px;

            color:
                var(--color-text);

            font-size:
                1.3rem;
        }


        .learner-stat small {
            color:
                var(--color-text-muted);

            font-size:
                .5rem;
        }


        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

        .learner-section {
            display:
                grid;

            gap:
                12px;
        }


        .section-heading {
            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                14px;
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

        .learner-grid {
            display:
                grid;

            grid-template-columns:
                repeat(
                    3,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap:
                12px;
        }


        .learner-card {
            min-height:
                190px;

            display:
                flex;

            flex-direction:
                column;

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


        .learner-card:hover {
            border-color:
                var(--brand-300);

            transform:
                translateY(
                    -1px
                );
        }


        .learner-card__icon {
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


        .learner-card__body {
            flex:
                1;
        }


        .learner-card h3 {
            margin:
                13px 0 4px;

            font-size:
                .74rem;
        }


        .learner-card p {
            margin:
                0;

            color:
                var(--color-text-muted);

            font-size:
                .55rem;

            line-height:
                1.6;
        }


        .learner-card__link {
            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

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


        .learner-card__disabled {
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
        | Student Workflow
        |--------------------------------------------------------------------------
        */

        .learner-workflow {
            display:
                grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap:
                10px;
        }


        .workflow-step {
            display:
                flex;

            align-items:
                flex-start;

            gap:
                10px;

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
            display:
                block;

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


            .learner-stats,
            .learner-grid,
            .learner-workflow {
                grid-template-columns:
                    1fr;
            }

        }

    </style>

</x-layouts.dashboard>