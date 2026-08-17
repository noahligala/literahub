<x-layouts.dashboard title="School Dashboard — LiteraHub">

    @php
        $school = auth()
            ->user()
            ->schools()
            ->first();
    @endphp

    <span class="eyebrow">
        Institution Portal
    </span>

    <h1>
        School Dashboard
    </h1>

    @if($school)

        <div class="dashboard-heading">

            <div>

                <h2>
                    {{ $school->name }}
                </h2>

                <p>
                    {{ ucfirst(
                        str_replace(
                            '_',
                            ' ',
                            $school->type ?? 'Institution'
                        )
                    ) }}

                    @if($school->town)
                        · {{ $school->town }}
                    @endif

                    @if($school->county)
                        · {{ $school->county }}
                    @endif
                </p>

            </div>

            <div>

                <span>
                    Status
                </span>

                <span
                    class="badge
                    {{ $school->status === 'active'
                        ? 'badge-success'
                        : 'badge-warning'
                    }}"
                >
                    {{ ucfirst($school->status) }}
                </span>

            </div>

        </div>

        <section class="cards">

            <article>

                <span class="eyebrow">
                    Learners
                </span>

                <h3>
                    Students
                </h3>

                <p>
                    Manage learners registered
                    under {{ $school->name }}.
                </p>

                <a
                    href="{{ route('school.students.index') }}"
                    class="button button-secondary button-small"
                >
                    Manage Students
                </a>

            </article>

            <article>

                <span class="eyebrow">
                    Staff
                </span>

                <h3>
                    Teachers
                </h3>

                <p>
                    Add teachers and control
                    their institutional access.
                </p>

                <a
                    href="{{ route('school.teachers.index') }}"
                    class="button button-secondary button-small"
                >
                    Manage Teachers
                </a>

            </article>

            <article>

                <span class="eyebrow">
                    Academic Structure
                </span>

                <h3>
                    Classes & Streams
                </h3>

                <p>
                    Create classes, streams,
                    courses and learning groups.
                </p>

                <a
                    href="{{ route('school.classes.index') }}"
                    class="button button-secondary button-small"
                >
                    Manage Classes
                </a>

            </article>

            <article>

                <span class="eyebrow">
                    Content
                </span>

                <h3>
                    Resource Library
                </h3>

                <p>
                    View literature resources available
                    under your institutional subscription.
                </p>

                <a
                    href="{{ route('school.library.index') }}"
                    class="button button-secondary button-small"
                >
                    Browse Resources
                </a>

            </article>

            <article>

                <span class="eyebrow">
                    Learning
                </span>

                <h3>
                    Assignments
                </h3>

                <p>
                    Review reading assignments,
                    assessments and completion.
                </p>

                <a
                    href="{{ route('school.assignments.index') }}"
                    class="button button-secondary button-small"
                >
                    View Assignments
                </a>

            </article>

            <article>

                <span class="eyebrow">
                    Billing
                </span>

                <h3>
                    Subscription
                </h3>

                <p>
                    View the institution's subscription,
                    licence limits and renewal information.
                </p>

                <a
                    href="{{ route('school.subscription.index') }}"
                    class="button button-secondary button-small"
                >
                    Manage Subscription
                </a>

            </article>

            <article>

                <span class="eyebrow">
                    Analytics
                </span>

                <h3>
                    Reports
                </h3>

                <p>
                    Review student participation,
                    reading activity and resource usage.
                </p>

                <a
                    href="{{ route('school.reports.index') }}"
                    class="button button-secondary button-small"
                >
                    View Reports
                </a>

            </article>

            <article>

                <span class="eyebrow">
                    Settings
                </span>

                <h3>
                    Institution Profile
                </h3>

                <p>
                    Update contact information
                    and institutional details.
                </p>

                <a
                    href="{{ route('school.profile.show') }}"
                    class="button button-secondary button-small"
                >
                    View Profile
                </a>

            </article>

        </section>

    @else

        <div class="alert alert-error">

            <h2>
                Institution not found
            </h2>

            <p>
                This account is marked as a school administrator
                but is not currently linked to an institution.
            </p>

        </div>

    @endif

</x-layouts.dashboard>