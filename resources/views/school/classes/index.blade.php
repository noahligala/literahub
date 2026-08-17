<x-layouts.dashboard title="Classes — LiteraHub">

    @php
        $school = auth()
            ->user()
            ->schools()
            ->first();
    @endphp

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Academic Structure
            </span>

            <h1>
                Classes & Streams
            </h1>

            <p>
                Organize learners into classes, streams
                and learning groups for
                {{ $school?->name ?? 'your institution' }}.
            </p>

        </div>

        <div class="actions">

            <button
                type="button"
                class="button"
            >
                + Add Class
            </button>

        </div>

    </div>


    <div class="metric-grid">

        <article>
            <strong>0</strong>
            <span>Total Classes</span>
        </article>

        <article>
            <strong>0</strong>
            <span>Active Students</span>
        </article>

        <article>
            <strong>0</strong>
            <span>Teachers Assigned</span>
        </article>

    </div>


    <div style="height: 14px;"></div>


    <div class="card">

        <div class="row-between">

            <div>

                <h3>
                    Class Directory
                </h3>

                <p>
                    View and manage classes and streams
                    within your institution.
                </p>

            </div>

            <div
                class="row"
                style="width: min(100%, 430px);"
            >

                <div style="flex: 1;">

                    <input
                        type="search"
                        placeholder="Search classes..."
                    >

                </div>

                <div style="width: min(100%, 150px);">

                    <select>

                        <option value="">
                            All Statuses
                        </option>

                        <option value="active">
                            Active
                        </option>

                        <option value="inactive">
                            Inactive
                        </option>

                    </select>

                </div>

            </div>

        </div>

    </div>


    <div style="height: 14px;"></div>


    <section class="cards">

        <article>

            <span class="eyebrow">
                Class
            </span>

            <div class="row-between">

                <div>

                    <h3>
                        Form 1
                    </h3>

                    <p>
                        General class group
                    </p>

                </div>

                <span class="badge badge-success">
                    Active
                </span>

            </div>

            <div style="height: 8px;"></div>

            <div class="class-stats">

                <div>
                    <strong>0</strong>
                    <span>Students</span>
                </div>

                <div>
                    <strong>0</strong>
                    <span>Teachers</span>
                </div>

                <div>
                    <strong>0</strong>
                    <span>Assignments</span>
                </div>

            </div>

            <div class="actions">

                <button
                    class="button button-secondary button-small"
                >
                    View Class
                </button>

                <button
                    class="button button-ghost button-small"
                >
                    Edit
                </button>

            </div>

        </article>


        <article>

            <span class="eyebrow">
                Class
            </span>

            <div class="row-between">

                <div>

                    <h3>
                        Form 2
                    </h3>

                    <p>
                        General class group
                    </p>

                </div>

                <span class="badge badge-success">
                    Active
                </span>

            </div>

            <div style="height: 8px;"></div>

            <div class="class-stats">

                <div>
                    <strong>0</strong>
                    <span>Students</span>
                </div>

                <div>
                    <strong>0</strong>
                    <span>Teachers</span>
                </div>

                <div>
                    <strong>0</strong>
                    <span>Assignments</span>
                </div>

            </div>

            <div class="actions">

                <button
                    class="button button-secondary button-small"
                >
                    View Class
                </button>

                <button
                    class="button button-ghost button-small"
                >
                    Edit
                </button>

            </div>

        </article>


        <article>

            <span class="eyebrow">
                Class
            </span>

            <div class="row-between">

                <div>

                    <h3>
                        Form 3
                    </h3>

                    <p>
                        General class group
                    </p>

                </div>

                <span class="badge badge-success">
                    Active
                </span>

            </div>

            <div style="height: 8px;"></div>

            <div class="class-stats">

                <div>
                    <strong>0</strong>
                    <span>Students</span>
                </div>

                <div>
                    <strong>0</strong>
                    <span>Teachers</span>
                </div>

                <div>
                    <strong>0</strong>
                    <span>Assignments</span>
                </div>

            </div>

            <div class="actions">

                <button
                    class="button button-secondary button-small"
                >
                    View Class
                </button>

                <button
                    class="button button-ghost button-small"
                >
                    Edit
                </button>

            </div>

        </article>


        <article>

            <span class="eyebrow">
                Class
            </span>

            <div class="row-between">

                <div>

                    <h3>
                        Form 4
                    </h3>

                    <p>
                        General class group
                    </p>

                </div>

                <span class="badge badge-success">
                    Active
                </span>

            </div>

            <div style="height: 8px;"></div>

            <div class="class-stats">

                <div>
                    <strong>0</strong>
                    <span>Students</span>
                </div>

                <div>
                    <strong>0</strong>
                    <span>Teachers</span>
                </div>

                <div>
                    <strong>0</strong>
                    <span>Assignments</span>
                </div>

            </div>

            <div class="actions">

                <button
                    class="button button-secondary button-small"
                >
                    View Class
                </button>

                <button
                    class="button button-ghost button-small"
                >
                    Edit
                </button>

            </div>

        </article>

    </section>


    <div style="height: 14px;"></div>


    <div class="card">

        <div class="row-between">

            <div>

                <h3>
                    Streams
                </h3>

                <p>
                    Create streams or learning groups
                    within each class.
                </p>

            </div>

            <button
                class="button button-secondary button-small"
            >
                + Add Stream
            </button>

        </div>


        <div style="height: 10px;"></div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>Stream</th>
                        <th>Class</th>
                        <th>Students</th>
                        <th>Class Teacher</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td colspan="6">

                            <div class="empty-state">

                                <h3>
                                    No streams yet
                                </h3>

                                <p>
                                    Streams created under your classes
                                    will appear here.
                                </p>

                                <button
                                    class="button button-small"
                                >
                                    Add First Stream
                                </button>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</x-layouts.dashboard>