<x-layouts.dashboard title="Teacher Dashboard — LiteraHub">

    @php
        $school = auth()
            ->user()
            ->schools()
            ->first();
    @endphp

    <span class="eyebrow">
        Teacher Portal
    </span>

    <h1>
        Welcome, {{ auth()->user()->name }}
    </h1>

    @if($school)

        <p>
            {{ $school->name }}
        </p>

    @endif

    <section class="cards">

        <article>
            <h3>My Classes</h3>

            <p>
                View classes and learner groups
                assigned to you.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                View Classes
            </a>
        </article>

        <article>
            <h3>Library</h3>

            <p>
                Browse literature resources available
                to your institution.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                Browse Library
            </a>
        </article>

        <article>
            <h3>Reading Lists</h3>

            <p>
                Build reading lists
                for your classes.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                Reading Lists
            </a>
        </article>

        <article>
            <h3>Assignments</h3>

            <p>
                Create reading assignments
                and assessments.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                Manage Assignments
            </a>
        </article>

        <article>
            <h3>Students</h3>

            <p>
                Review learner activity
                and reading progress.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                View Students
            </a>
        </article>

        <article>
            <h3>Performance</h3>

            <p>
                Review assessment and
                reading-performance reports.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                View Performance
            </a>
        </article>

    </section>

</x-layouts.dashboard>