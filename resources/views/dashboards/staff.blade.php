<x-layouts.dashboard title="Staff Dashboard — LiteraHub">

    <span class="eyebrow">
        Platform Staff
    </span>

    <h1>
        Staff Dashboard
    </h1>

    <p>
        Welcome, {{ auth()->user()->name }}.
    </p>

    <p>
        Your available functions depend on your
        LiteraHub role and permissions.
    </p>

    <section class="cards">

        @can('manage resources')
            <article>
                <h3>Resources</h3>

                <p>
                    Create, review and manage
                    literary resources.
                </p>

                <a
                    href="#"
                    class="button button-secondary button-small"
                >
                    Manage Resources
                </a>
            </article>
        @endcan

        @can('publish resources')
            <article>
                <h3>Publishing</h3>

                <p>
                    Review resources awaiting
                    publication approval.
                </p>

                <a
                    href="#"
                    class="button button-secondary button-small"
                >
                    Publishing Queue
                </a>
            </article>
        @endcan

        @can('manage payments')
            <article>
                <h3>Finance</h3>

                <p>
                    Review payments and
                    subscription transactions.
                </p>

                <a
                    href="#"
                    class="button button-secondary button-small"
                >
                    View Finance
                </a>
            </article>
        @endcan

        @can('view reports')
            <article>
                <h3>Reports</h3>

                <p>
                    View available platform reports
                    and analytics.
                </p>

                <a
                    href="#"
                    class="button button-secondary button-small"
                >
                    View Reports
                </a>
            </article>
        @endcan

    </section>

</x-layouts.dashboard>