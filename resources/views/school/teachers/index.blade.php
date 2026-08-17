<x-layouts.dashboard title="Teachers — LiteraHub">

    @php
        $school = auth()
            ->user()
            ->schools()
            ->first();
    @endphp

    <div class="dashboard-heading">

        <div>
            <span class="eyebrow">
                Staff Management
            </span>

            <h1>
                Teachers
            </h1>

            <p>
                Manage teachers and teaching access for
                {{ $school?->name ?? 'your institution' }}.
            </p>
        </div>

        <div class="actions">

            <button
                type="button"
                class="button"
            >
                + Add Teacher
            </button>

        </div>

    </div>

    <div class="metric-grid">

        <article>
            <strong>0</strong>
            <span>Total Teachers</span>
        </article>

        <article>
            <strong>0</strong>
            <span>Active</span>
        </article>

        <article>
            <strong>0</strong>
            <span>Classes Assigned</span>
        </article>

    </div>

    <div style="height: 14px;"></div>

    <div class="card">

        <div class="row-between">

            <div>
                <h3>
                    Teaching Staff
                </h3>

                <p>
                    Review teacher accounts and assignments.
                </p>
            </div>

            <div style="width: min(100%, 260px);">
                <input
                    type="search"
                    placeholder="Search teachers..."
                >
            </div>

        </div>

        <div style="height: 12px;"></div>

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>Teacher</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Classes</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td colspan="6">

                            <div class="empty-state">

                                <h3>
                                    No teachers yet
                                </h3>

                                <p>
                                    Add your teaching staff to begin
                                    assigning classes and resources.
                                </p>

                                <button class="button button-small">
                                    Add First Teacher
                                </button>

                            </div>

                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</x-layouts.dashboard>