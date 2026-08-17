<x-layouts.dashboard title="Student Report — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Reports
            </span>

            <h1>
                Student Activity
            </h1>

            <p>
                Review learner engagement and usage.
            </p>

        </div>

        <div class="actions">

            <button
                type="button"
                class="button button-secondary"
            >
                Export Report
            </button>

        </div>

    </div>


    <x-forms.report-filters
        :classes="$classes ?? collect()"
        :teachers="$teachers ?? collect()"
    />


    <div style="height: 14px;"></div>


    <div class="metric-grid">

        <article>
            <strong>
                {{ $metrics['students'] ?? 0 }}
            </strong>

            <span>Students</span>
        </article>

        <article>
            <strong>
                {{ $metrics['active'] ?? 0 }}
            </strong>

            <span>Active Readers</span>
        </article>

        <article>
            <strong>
                {{ $metrics['completion'] ?? 0 }}%
            </strong>

            <span>Completion</span>
        </article>

    </div>


    <div style="height: 14px;"></div>


    <div class="card">

        <h3>
            Student Activity
        </h3>

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Resources Opened</th>
                        <th>Assignments</th>
                        <th>Completion</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($rows ?? [] as $row)

                        <tr>

                            <td>
                                {{ $row['student'] }}
                            </td>

                            <td>
                                {{ $row['class'] }}
                            </td>

                            <td>
                                {{ $row['resources'] }}
                            </td>

                            <td>
                                {{ $row['assignments'] }}
                            </td>

                            <td>
                                {{ $row['completion'] }}%
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5">

                                <div class="empty-state">

                                    <h3>
                                        No report data
                                    </h3>

                                    <p>
                                        Report information will appear
                                        when learner activity is recorded.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-layouts.dashboard>