<x-layouts.dashboard title="Assignments — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Academic Activities
            </span>

            <h1>
                Assignments
            </h1>

            <p>
                Track literature assignments,
                assessments and learner completion.
            </p>

        </div>

        <div class="actions">

            <button class="button">
                + New Assignment
            </button>

        </div>

    </div>

    <div class="metric-grid">

        <article>
            <strong>0</strong>
            <span>Active Assignments</span>
        </article>

        <article>
            <strong>0</strong>
            <span>Due This Week</span>
        </article>

        <article>
            <strong>0%</strong>
            <span>Average Completion</span>
        </article>

    </div>

    <div style="height: 14px;"></div>

    <div class="card">

        <div class="row-between">

            <div>
                <h3>
                    Assignment Overview
                </h3>

                <p>
                    Review assignments created by teachers.
                </p>
            </div>

            <div style="width: min(100%, 180px);">
                <select>
                    <option>All Assignments</option>
                    <option>Active</option>
                    <option>Upcoming</option>
                    <option>Completed</option>
                    <option>Overdue</option>
                </select>
            </div>

        </div>

        <div style="height: 12px;"></div>

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>Assignment</th>
                        <th>Class</th>
                        <th>Teacher</th>
                        <th>Due Date</th>
                        <th>Completion</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>

                        <td colspan="6">

                            <div class="empty-state">

                                <h3>
                                    No assignments yet
                                </h3>

                                <p>
                                    Assignments created by teachers
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