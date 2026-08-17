<x-layouts.dashboard title="Subscription — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Billing & Access
            </span>

            <h1>
                Subscription
            </h1>

            <p>
                Manage your institution's LiteraHub
                plan, licences and billing.
            </p>

        </div>

    </div>

    <div class="cards">

        <article>

            <span class="eyebrow">
                Current Plan
            </span>

            <h2>
                No Active Plan
            </h2>

            <p>
                Choose a school subscription to activate
                learner and teacher access.
            </p>

            <a
                href="{{ route('pricing') }}"
                class="button"
            >
                View Plans
            </a>

        </article>

        <article>

            <span class="eyebrow">
                Student Licences
            </span>

            <h2>
                0 / 0
            </h2>

            <p>
                No student licences are currently allocated.
            </p>

            <div class="progress">
                <span style="width: 0%"></span>
            </div>

        </article>

        <article>

            <span class="eyebrow">
                Teacher Licences
            </span>

            <h2>
                0 / 0
            </h2>

            <p>
                No teacher licences are currently allocated.
            </p>

            <div class="progress">
                <span style="width: 0%"></span>
            </div>

        </article>

        <article>

            <span class="eyebrow">
                Renewal
            </span>

            <h2>
                —
            </h2>

            <p>
                Your renewal information will appear
                once a subscription is active.
            </p>

        </article>

    </div>

    <div style="height: 14px;"></div>

    <div class="card">

        <div class="row-between">

            <div>

                <h3>
                    Payment History
                </h3>

                <p>
                    Review subscription and licence payments.
                </p>

            </div>

        </div>

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td colspan="6">

                            <div class="empty-state">

                                <h3>
                                    No payments yet
                                </h3>

                                <p>
                                    Completed subscription payments
                                    will appear here.
                                </p>

                            </div>

                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</x-layouts.dashboard>