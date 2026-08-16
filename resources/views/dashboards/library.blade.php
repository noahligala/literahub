<x-layouts.dashboard title="My Library — LiteraHub">

    <span class="eyebrow">
        Learner Portal
    </span>

    <h1>
        Welcome, {{ auth()->user()->name }}
    </h1>

    <p>
        Access literature, continue reading
        and monitor your learning activity.
    </p>

    <section class="cards">

        <article>
            <h3>My Library</h3>

            <p>
                Browse all literature resources
                included in your subscription.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                Browse Library
            </a>
        </article>

        <article>
            <h3>Continue Reading</h3>

            <p>
                Resume books from your
                most recent reading position.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                Continue Reading
            </a>
        </article>

        <article>
            <h3>Bookmarks</h3>

            <p>
                Review books, chapters
                and pages you have saved.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                View Bookmarks
            </a>
        </article>

        <article>
            <h3>My Notes</h3>

            <p>
                Review highlights and
                personal reading notes.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                View Notes
            </a>
        </article>

        <article>
            <h3>Assignments</h3>

            <p>
                View reading tasks,
                quizzes and upcoming deadlines.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                View Assignments
            </a>
        </article>

        <article>
            <h3>Reading Progress</h3>

            <p>
                Review your reading activity
                and completed resources.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                View Progress
            </a>
        </article>

        <article>
            <h3>Subscription</h3>

            <p>
                Review your current plan,
                access status and renewal date.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                Manage Subscription
            </a>
        </article>

        <article>
            <h3>Profile</h3>

            <p>
                Update your personal
                and education details.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                Manage Profile
            </a>
        </article>

    </section>

</x-layouts.dashboard>