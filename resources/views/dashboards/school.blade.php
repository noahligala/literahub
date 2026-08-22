<x-layouts.dashboard title="School Dashboard — LiteraHub">

    @php

        $user = auth()->user();

        $school = $user
            ->activeSchools()
            ->first();

    @endphp


    <div class="school-dashboard">

        <span class="eyebrow">
            Institution Portal
        </span>


        <h1>
            School Dashboard
        </h1>


        @if ($school)

            {{-- =============================================================
                Institution Heading
            ============================================================== --}}

            <div class="dashboard-heading">

                <div>

                    <h2>
                        {{ $school->name }}
                    </h2>

                    <p>

                        {{
                            ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    $school->type
                                        ?? 'institution'
                                )
                            )
                        }}

                        @if ($school->town)

                            · {{ $school->town }}

                        @endif


                        @if ($school->county)

                            · {{ $school->county }}

                        @endif

                    </p>

                </div>


                <div class="institution-status">

                    <span>
                        Status
                    </span>

                    <span
                        class="badge {{
                            $school->status === 'active'
                                ? 'badge-success'
                                : 'badge-warning'
                        }}"
                    >

                        {{
                            ucfirst(
                                $school->status
                            )
                        }}

                    </span>

                </div>

            </div>


            {{-- =============================================================
                Primary Management Cards
            ============================================================== --}}

            <section class="cards">


                {{-- Students --}}

                <article>

                    <span class="eyebrow">
                        Learners
                    </span>

                    <h3>
                        Students
                    </h3>

                    <p>
                        Manage learners registered under
                        {{ $school->name }}, their status,
                        class placement and institutional access.
                    </p>

                    <a
                        href="{{ route(
                            'school.students.index'
                        ) }}"
                        class="button button-secondary button-small"
                    >
                        Manage Students
                    </a>

                </article>


                {{-- Teachers --}}

                <article>

                    <span class="eyebrow">
                        Staff
                    </span>

                    <h3>
                        Teachers
                    </h3>

                    <p>
                        Manage teachers, teaching access,
                        classes and institutional permissions.
                    </p>

                    <a
                        href="{{ route(
                            'school.teachers.index'
                        ) }}"
                        class="button button-secondary button-small"
                    >
                        Manage Teachers
                    </a>

                </article>


                {{-- Classes --}}

                <article>

                    <span class="eyebrow">
                        Academic Structure
                    </span>

                    <h3>
                        Classes & Streams
                    </h3>

                    <p>
                        Create classes and streams, place learners
                        and assign teachers to learning groups.
                    </p>

                    <a
                        href="{{ route(
                            'school.classes.index'
                        ) }}"
                        class="button button-secondary button-small"
                    >
                        Manage Classes
                    </a>

                </article>


                {{-- Library --}}

                <article>

                    <span class="eyebrow">
                        Content
                    </span>

                    <h3>
                        Resource Library
                    </h3>

                    <p>
                        Browse literature currently licensed to
                        your institution and available under
                        active content rights.
                    </p>

                    <a
                        href="{{ route(
                            'school.library.index'
                        ) }}"
                        class="button button-secondary button-small"
                    >
                        Browse Resources
                    </a>

                </article>


                {{-- Assignments --}}

                <article>

                    <span class="eyebrow">
                        Learning
                    </span>

                    <h3>
                        Assignments
                    </h3>

                    <p>
                        Review assignments created by teachers,
                        student allocation, submissions,
                        deadlines and grading activity.
                    </p>

                    <a
                        href="{{ route(
                            'school.assignments.index'
                        ) }}"
                        class="button button-secondary button-small"
                    >
                        Manage Assignments
                    </a>

                </article>


                {{-- Subscription --}}

                <article>

                    <span class="eyebrow">
                        Billing
                    </span>

                    <h3>
                        Subscription
                    </h3>

                    <p>
                        Review the institution subscription,
                        licence limits, access status and
                        renewal information.
                    </p>

                    <a
                        href="{{ route(
                            'school.subscription.index'
                        ) }}"
                        class="button button-secondary button-small"
                    >
                        Manage Subscription
                    </a>

                </article>


                {{-- Reports --}}

                <article>

                    <span class="eyebrow">
                        Analytics
                    </span>

                    <h3>
                        Reports
                    </h3>

                    <p>
                        Review student participation, reading
                        activity, assignment completion,
                        resource usage and licence utilisation.
                    </p>

                    <a
                        href="{{ route(
                            'school.reports.index'
                        ) }}"
                        class="button button-secondary button-small"
                    >
                        View Reports
                    </a>

                </article>


                {{-- Profile --}}

                <article>

                    <span class="eyebrow">
                        Settings
                    </span>

                    <h3>
                        Institution Profile
                    </h3>

                    <p>
                        Review and update institutional
                        information and contact details.
                    </p>

                    <a
                        href="{{ route(
                            'school.profile.show'
                        ) }}"
                        class="button button-secondary button-small"
                    >
                        View Profile
                    </a>

                </article>

            </section>


            {{-- =============================================================
                Academic Workflow
            ============================================================== --}}

            <section class="workflow-section">

                <div class="section-heading">

                    <div>

                        <span class="eyebrow">
                            Academic Workflow
                        </span>

                        <h2>
                            Teaching & Assignment Cycle
                        </h2>

                    </div>

                </div>


                <div class="workflow-grid">


                    <div class="workflow-step">

                        <span>
                            1
                        </span>

                        <div>

                            <strong>
                                Licence Resources
                            </strong>

                            <p>
                                Ensure the school has active
                                licences for required literature.
                            </p>

                        </div>

                    </div>


                    <div class="workflow-step">

                        <span>
                            2
                        </span>

                        <div>

                            <strong>
                                Assign Teachers
                            </strong>

                            <p>
                                Connect teachers with their
                                classes and learner groups.
                            </p>

                        </div>

                    </div>


                    <div class="workflow-step">

                        <span>
                            3
                        </span>

                        <div>

                            <strong>
                                Create Assignments
                            </strong>

                            <p>
                                Teachers select licensed books,
                                reading ranges and deadlines.
                            </p>

                        </div>

                    </div>


                    <div class="workflow-step">

                        <span>
                            4
                        </span>

                        <div>

                            <strong>
                                Review Outcomes
                            </strong>

                            <p>
                                Monitor submissions, grading,
                                feedback and student participation.
                            </p>

                        </div>

                    </div>

                </div>

            </section>

        @else

            <div class="alert alert-error">

                <h2>
                    Institution not found
                </h2>

                <p>
                    This account is marked as a school
                    administrator but is not currently linked
                    to an active institution.
                </p>

            </div>

        @endif

    </div>


    <style>

        .school-dashboard {
            display: grid;
            gap: 22px;
        }


        .institution-status {
            display: flex;
            align-items: center;
            gap: 8px;
        }


        .workflow-section {
            display: grid;
            gap: 12px;
            margin-top: 4px;
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


        .workflow-grid {
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
            max-width: 900px
        ) {

            .workflow-grid {
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
            max-width: 600px
        ) {

            .workflow-grid {
                grid-template-columns:
                    1fr;
            }

        }

    </style>

</x-layouts.dashboard>