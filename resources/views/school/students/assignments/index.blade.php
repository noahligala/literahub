<x-layouts.dashboard title="My Assignments — LiteraHub">
<div class="assignments-page">

    <div class="page-shell">

        {{-- ================================================================
            Header
        ================================================================= --}}

        <div class="page-header">
            <div>
                <div class="eyebrow">
                    Learning
                </div>

                <h1>
                    My Assignments
                </h1>

                <p>
                    View your current reading assignments,
                    deadlines, submissions and results.
                </p>
            </div>

            <a
                href="{{ route('school.library.index') }}"
                class="btn btn-secondary"
            >
                Browse Library
            </a>
        </div>


        {{-- ================================================================
            Flash Messages
        ================================================================= --}}

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        {{-- ================================================================
            Metrics
        ================================================================= --}}

        <div class="metric-grid">

            <div class="metric-card">
                <span class="metric-label">
                    Total
                </span>

                <strong>
                    {{ $totalAssignments }}
                </strong>
            </div>

            <div class="metric-card">
                <span class="metric-label">
                    Submitted
                </span>

                <strong>
                    {{ $submittedAssignments }}
                </strong>
            </div>

            <div class="metric-card">
                <span class="metric-label">
                    Graded
                </span>

                <strong>
                    {{ $gradedAssignments }}
                </strong>
            </div>

            <div class="metric-card">
                <span class="metric-label">
                    Overdue
                </span>

                <strong>
                    {{ $overdueAssignments }}
                </strong>
            </div>

        </div>


        {{-- ================================================================
            Search / Filters
        ================================================================= --}}

        <form
            method="GET"
            action="{{ route('student.assignments.index') }}"
            class="filter-panel"
        >

            <div class="filter-search">
                <label for="search">
                    Search assignments
                </label>

                <input
                    id="search"
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search by assignment, book or teacher..."
                >
            </div>


            <div class="filter-status">
                <label for="status">
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                >
                    <option value="">
                        All Assignments
                    </option>

                    <option
                        value="available"
                        @selected(request('status') === 'available')
                    >
                        Available
                    </option>

                    <option
                        value="upcoming"
                        @selected(request('status') === 'upcoming')
                    >
                        Upcoming
                    </option>

                    <option
                        value="overdue"
                        @selected(request('status') === 'overdue')
                    >
                        Overdue
                    </option>

                    <option
                        value="submitted"
                        @selected(request('status') === 'submitted')
                    >
                        Submitted
                    </option>

                    <option
                        value="graded"
                        @selected(request('status') === 'graded')
                    >
                        Graded
                    </option>
                </select>
            </div>


            <div class="filter-actions">
                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Filter
                </button>

                @if (
                    request()->filled('search')
                    || request()->filled('status')
                )
                    <a
                        href="{{ route('student.assignments.index') }}"
                        class="btn btn-secondary"
                    >
                        Clear
                    </a>
                @endif
            </div>

        </form>


        {{-- ================================================================
            Assignment List
        ================================================================= --}}

        <div class="assignment-list">

            @forelse ($assignments as $assignment)

                @php
                    $submission = $assignment
                        ->submissions
                        ->first();

                    $submissionStatus =
                        $submission?->status;

                    $hasStarted =
                        ! $assignment->starts_at
                        || now()->greaterThanOrEqualTo(
                            $assignment->starts_at
                        );

                    $pastDue =
                        $assignment->due_at
                        && now()->greaterThan(
                            $assignment->due_at
                        );

                    $submitted = in_array(
                        $submissionStatus,
                        [
                            'submitted',
                            'late',
                            'graded',
                        ],
                        true
                    );

                    if ($submissionStatus === 'graded') {
                        $displayStatus = 'Graded';
                        $statusClass = 'status-graded';
                    } elseif ($submissionStatus === 'late') {
                        $displayStatus = 'Submitted Late';
                        $statusClass = 'status-late';
                    } elseif ($submissionStatus === 'submitted') {
                        $displayStatus = 'Submitted';
                        $statusClass = 'status-submitted';
                    } elseif ($submissionStatus === 'draft') {
                        $displayStatus = 'Draft';
                        $statusClass = 'status-draft';
                    } elseif (! $hasStarted) {
                        $displayStatus = 'Upcoming';
                        $statusClass = 'status-upcoming';
                    } elseif ($pastDue) {
                        $displayStatus = 'Overdue';
                        $statusClass = 'status-overdue';
                    } else {
                        $displayStatus = 'Available';
                        $statusClass = 'status-available';
                    }
                @endphp


                <article class="assignment-card">

                    <div class="assignment-main">

                        <div class="assignment-heading">

                            <span class="status-badge {{ $statusClass }}">
                                {{ $displayStatus }}
                            </span>

                            <h2>
                                <a
                                    href="{{ route(
                                        'student.assignments.show',
                                        $assignment
                                    ) }}"
                                >
                                    {{ $assignment->title }}
                                </a>
                            </h2>

                        </div>


                        <div class="assignment-meta">

                            @if ($assignment->schoolClass)
                                <span>
                                    <strong>Class:</strong>

                                    {{ $assignment->schoolClass->name }}
                                </span>
                            @endif


                            @if ($assignment->creator)
                                <span>
                                    <strong>Teacher:</strong>

                                    {{ $assignment->creator->name }}
                                </span>
                            @endif


                            @if ($assignment->book)
                                <span>
                                    <strong>Book:</strong>

                                    {{ $assignment->book->title }}
                                </span>
                            @endif

                        </div>


                        @if ($assignment->instructions)
                            <p class="assignment-description">
                                {{
                                    \Illuminate\Support\Str::limit(
                                        strip_tags(
                                            $assignment->instructions
                                        ),
                                        180
                                    )
                                }}
                            </p>
                        @endif

                    </div>


                    <div class="assignment-details">

                        <div class="detail">

                            <span>
                                Available
                            </span>

                            <strong>
                                {{
                                    $assignment->starts_at
                                        ? $assignment
                                            ->starts_at
                                            ->format('d M Y, H:i')
                                        : 'Immediately'
                                }}
                            </strong>

                        </div>


                        <div class="detail">

                            <span>
                                Due
                            </span>

                            <strong>
                                {{
                                    $assignment->due_at
                                        ? $assignment
                                            ->due_at
                                            ->format('d M Y, H:i')
                                        : 'No deadline'
                                }}
                            </strong>

                        </div>


                        @if ($assignment->total_marks)
                            <div class="detail">

                                <span>
                                    Marks
                                </span>

                                <strong>
                                    {{ $assignment->total_marks }}
                                </strong>

                            </div>
                        @endif


                        @if (
                            $submission
                            && $submission->status === 'graded'
                        )
                            <div class="detail">

                                <span>
                                    Score
                                </span>

                                <strong>
                                    {{ $submission->score ?? '—' }}

                                    @if ($assignment->total_marks)
                                        / {{ $assignment->total_marks }}
                                    @endif
                                </strong>

                            </div>
                        @endif

                    </div>


                    <div class="assignment-actions">

                        @if ($assignment->book)
                            <a
                                href="{{ route(
                                    'school.library.show',
                                    $assignment->book
                                ) }}"
                                class="btn btn-secondary"
                            >
                                View Book
                            </a>
                        @endif


                        <a
                            href="{{ route(
                                'student.assignments.show',
                                $assignment
                            ) }}"
                            class="btn btn-primary"
                        >
                            @if ($submissionStatus === 'graded')
                                View Result
                            @elseif ($submitted)
                                View Submission
                            @elseif ($submissionStatus === 'draft')
                                Continue Assignment
                            @else
                                Open Assignment
                            @endif
                        </a>

                    </div>

                </article>

            @empty

                <div class="empty-state">

                    <div class="empty-icon">
                        A
                    </div>

                    <h2>
                        No assignments found
                    </h2>

                    <p>
                        There are currently no assignments
                        matching your filters.
                    </p>

                    @if (
                        request()->filled('search')
                        || request()->filled('status')
                    )
                        <a
                            href="{{ route(
                                'student.assignments.index'
                            ) }}"
                            class="btn btn-secondary"
                        >
                            Clear Filters
                        </a>
                    @endif

                </div>

            @endforelse

        </div>


        {{-- ================================================================
            Pagination
        ================================================================= --}}

        @if ($assignments->hasPages())
            <div class="pagination-wrap">
                {{ $assignments->links() }}
            </div>
        @endif

    </div>

</div>


<!-- <style>

.assignments-page {
    min-height: 100vh;
    background: #f7f9fc;
    color: #172033;
    padding: 32px 20px 64px;
}

.page-shell {
    width: min(1180px, 100%);
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    margin-bottom: 28px;
}

.eyebrow {
    color: #067a89;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    margin-bottom: 7px;
}

.page-header h1 {
    margin: 0;
    color: #002b5c;
    font-size: clamp(28px, 4vw, 40px);
    line-height: 1.1;
}

.page-header p {
    margin: 10px 0 0;
    max-width: 650px;
    color: #667085;
    line-height: 1.6;
}

.metric-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 24px;
}

.metric-card {
    background: #ffffff;
    border: 1px solid #e5eaf0;
    padding: 20px;
}

.metric-label {
    display: block;
    color: #667085;
    font-size: 13px;
    margin-bottom: 8px;
}

.metric-card strong {
    color: #002b5c;
    font-size: 28px;
}

.filter-panel {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 220px auto;
    gap: 14px;
    align-items: end;
    background: #ffffff;
    border: 1px solid #e5eaf0;
    padding: 18px;
    margin-bottom: 22px;
}

.filter-panel label {
    display: block;
    margin-bottom: 7px;
    color: #344054;
    font-size: 13px;
    font-weight: 600;
}

.filter-panel input,
.filter-panel select {
    width: 100%;
    min-height: 44px;
    border: 1px solid #d0d5dd;
    background: #ffffff;
    color: #172033;
    padding: 10px 12px;
    font: inherit;
}

.filter-actions {
    display: flex;
    gap: 8px;
}

.assignment-list {
    display: grid;
    gap: 16px;
}

.assignment-card {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 210px;
    gap: 24px;
    background: #ffffff;
    border: 1px solid #e5eaf0;
    padding: 22px;
}

.assignment-heading {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 12px;
}

.assignment-heading h2 {
    width: 100%;
    margin: 2px 0 0;
    font-size: 21px;
}

.assignment-heading h2 a {
    color: #002b5c;
    text-decoration: none;
}

.assignment-heading h2 a:hover {
    text-decoration: underline;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 5px 9px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
}

.status-available {
    background: #e8f7ef;
    color: #117a46;
}

.status-upcoming {
    background: #e8f0fa;
    color: #315d94;
}

.status-overdue,
.status-late {
    background: #fff1ef;
    color: #b42318;
}

.status-submitted {
    background: #ddf5f7;
    color: #067a89;
}

.status-graded {
    background: #fff7dc;
    color: #8b6500;
}

.status-draft {
    background: #f2f4f7;
    color: #475467;
}

.assignment-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 20px;
    margin-top: 14px;
    color: #667085;
    font-size: 13px;
}

.assignment-meta strong {
    color: #344054;
}

.assignment-description {
    margin: 15px 0 0;
    color: #475467;
    line-height: 1.6;
}

.assignment-details {
    display: grid;
    gap: 12px;
    border-left: 1px solid #eaecf0;
    padding-left: 20px;
}

.detail span {
    display: block;
    color: #667085;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 3px;
}

.detail strong {
    color: #344054;
    font-size: 13px;
}

.assignment-actions {
    grid-column: 1 / -1;
    display: flex;
    justify-content: flex-end;
    gap: 9px;
    border-top: 1px solid #eaecf0;
    padding-top: 16px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 9px 16px;
    border: 1px solid transparent;
    text-decoration: none;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
}

.btn-primary {
    background: #002b5c;
    border-color: #002b5c;
    color: #ffffff;
}

.btn-primary:hover {
    background: #003c78;
}

.btn-secondary {
    background: #ffffff;
    color: #002b5c;
    border-color: #cbd5e1;
}

.alert {
    padding: 14px 16px;
    margin-bottom: 20px;
    border: 1px solid;
}

.alert-success {
    background: #ecfdf3;
    border-color: #abefc6;
    color: #067647;
}

.empty-state {
    background: #ffffff;
    border: 1px solid #e5eaf0;
    padding: 56px 24px;
    text-align: center;
}

.empty-icon {
    display: grid;
    place-items: center;
    width: 50px;
    height: 50px;
    margin: 0 auto 16px;
    background: #ddf5f7;
    color: #007b88;
    font-weight: 800;
}

.empty-state h2 {
    margin: 0 0 8px;
    color: #002b5c;
}

.empty-state p {
    color: #667085;
    margin-bottom: 20px;
}

.pagination-wrap {
    margin-top: 24px;
}

@media (max-width: 850px) {

    .metric-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

    .filter-panel {
        grid-template-columns: 1fr;
    }

    .assignment-card {
        grid-template-columns: 1fr;
    }

    .assignment-details {
        border-left: 0;
        border-top: 1px solid #eaecf0;
        padding: 16px 0 0;
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

}

@media (max-width: 600px) {

    .assignments-page {
        padding:
            22px 14px
            48px;
    }

    .page-header {
        flex-direction: column;
    }

    .metric-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

    .assignment-card {
        padding: 17px;
    }

    .assignment-actions {
        flex-direction: column;
    }

    .assignment-actions .btn {
        width: 100%;
    }

}

</style> -->

</x-layouts.dashboard>