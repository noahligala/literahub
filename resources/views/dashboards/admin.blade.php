<x-layouts.dashboard title="Administration — LiteraHub">

    <span class="eyebrow">
        Platform Administration
    </span>

    <h1>
        LiteraHub Administration
    </h1>

    <p>
        Welcome, {{ auth()->user()->name }}.
        Manage the LiteraHub platform from this dashboard.
    </p>

    <section class="cards">

        <article>
            <h3>Schools</h3>

            <p>
                Review institutions, approve registrations
                and manage school accounts.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                Manage Schools
            </a>
        </article>

        <article>
            <h3>Resources</h3>

            <p>
                Manage books, literary works, study guides
                and educational resources.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                Manage Resources
            </a>
        </article>

        <article>
            <h3>Authors</h3>

            <p>
                Manage authors, publishers
                and content contributors.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                Manage Authors
            </a>
        </article>

        <article>
            <h3>Subscriptions</h3>

            <p>
                Review plans, institutional subscriptions
                and individual learner subscriptions.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                Manage Subscriptions
            </a>
        </article>

        <article>
            <h3>Payments</h3>

            <p>
                Review M-Pesa, card and institutional
                payment transactions.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                View Payments
            </a>
        </article>

        <article>
            <h3>Reports</h3>

            <p>
                View platform adoption, revenue
                and resource engagement statistics.
            </p>

            <a
                href="#"
                class="button button-secondary button-small"
            >
                View Reports
            </a>
        </article>

    </section>

</x-layouts.dashboard>