<x-layouts.dashboard :title="$assignment->title . ' — LiteraHub'">

@php

    /*
    |--------------------------------------------------------------------------
    | Submission State
    |--------------------------------------------------------------------------
    */

    $submissionStatus = $submission?->status;


    /*
    |--------------------------------------------------------------------------
    | Display Status
    |--------------------------------------------------------------------------
    */

    if ($isGraded) {

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

    } elseif ($isPastDue) {

        $displayStatus = 'Overdue';
        $statusClass = 'status-overdue';

    } else {

        $displayStatus = 'Available';
        $statusClass = 'status-available';

    }


    /*
    |--------------------------------------------------------------------------
    | Score Percentage
    |--------------------------------------------------------------------------
    */

    $percentage = null;

    if (
        $isGraded
        && $submission?->score !== null
        && $assignment->total_marks
        && $assignment->total_marks > 0
    ) {
        $percentage = round(
            (
                $submission->score
                / $assignment->total_marks
            ) * 100,
            1
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Existing Student Response
    |--------------------------------------------------------------------------
    */

    $currentResponse = old(
        'response',
        $submission?->response ?? ''
    );

@endphp


<div class="assignment-page">

    <div class="page-shell">


        {{-- ================================================================
            Navigation
        ================================================================= --}}

        <div class="top-navigation">

            <a
                href="{{ route(
                    'student.assignments.index'
                ) }}"
                class="back-link"
            >
                ← My Assignments
            </a>

        </div>


        {{-- ================================================================
            Flash Messages
        ================================================================= --}}

        @if (session('success'))

            <div
                class="alert alert-success"
                role="status"
            >
                {{ session('success') }}
            </div>

        @endif


        @if (session('error'))

            <div
                class="alert alert-error"
                role="alert"
            >
                {{ session('error') }}
            </div>

        @endif


        {{-- ================================================================
            Validation Errors
        ================================================================= --}}

        @if ($errors->any())

            <div
                class="alert alert-error"
                role="alert"
            >

                <strong>
                    Please review the following:
                </strong>

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ================================================================
            Assignment Header
        ================================================================= --}}

        <section class="assignment-header">

            <div class="assignment-header-main">

                <span class="status-badge {{ $statusClass }}">
                    {{ $displayStatus }}
                </span>


                <h1>
                    {{ $assignment->title }}
                </h1>


                <div class="header-meta">

                    @if ($assignment->schoolClass)

                        <span>

                            <strong>
                                Class:
                            </strong>

                            {{ $assignment->schoolClass->name }}

                        </span>

                    @endif


                    @if ($assignment->creator)

                        <span>

                            <strong>
                                Teacher:
                            </strong>

                            {{ $assignment->creator->name }}

                        </span>

                    @endif


                    @if ($assignment->book)

                        <span>

                            <strong>
                                Book:
                            </strong>

                            {{ $assignment->book->title }}

                        </span>

                    @endif

                </div>

            </div>


            @if ($assignment->total_marks)

                <div class="marks-card">

                    <span>
                        Total Marks
                    </span>

                    <strong>
                        {{ $assignment->total_marks }}
                    </strong>

                </div>

            @endif

        </section>


        {{-- ================================================================
            Timeline
        ================================================================= --}}

        <section class="timeline-grid">


            {{-- Available From --}}

            <div class="timeline-card">

                <span class="timeline-label">
                    Available From
                </span>

                <strong>

                    {{
                        $assignment->starts_at
                            ? $assignment
                                ->starts_at
                                ->format(
                                    'd M Y, H:i'
                                )
                            : 'Immediately'
                    }}

                </strong>

            </div>


            {{-- Due Date --}}

            <div class="timeline-card">

                <span class="timeline-label">
                    Due Date
                </span>

                <strong
                    class="{{
                        $isPastDue
                        && ! $isSubmitted
                            ? 'danger-text'
                            : ''
                    }}"
                >

                    {{
                        $assignment->due_at
                            ? $assignment
                                ->due_at
                                ->format(
                                    'd M Y, H:i'
                                )
                            : 'No deadline'
                    }}

                </strong>

            </div>


            {{-- Submitted At --}}

            @if ($submission?->submitted_at)

                <div class="timeline-card">

                    <span class="timeline-label">
                        Submitted
                    </span>

                    <strong>

                        {{
                            $submission
                                ->submitted_at
                                ->format(
                                    'd M Y, H:i'
                                )
                        }}

                    </strong>

                </div>

            @endif


            {{-- Graded At --}}

            @if ($submission?->graded_at)

                <div class="timeline-card">

                    <span class="timeline-label">
                        Graded
                    </span>

                    <strong>

                        {{
                            $submission
                                ->graded_at
                                ->format(
                                    'd M Y, H:i'
                                )
                        }}

                    </strong>

                </div>

            @endif

        </section>


        {{-- ================================================================
            Main Layout
        ================================================================= --}}

        <div class="content-grid">


            {{-- ============================================================
                Main Column
            ============================================================= --}}

            <main class="main-column">


                {{-- ========================================================
                    Assigned Book
                ========================================================= --}}

                @if ($assignment->book)

                    <section class="panel">

                        <div class="panel-heading">

                            <div>

                                <span class="section-label">
                                    Reading Material
                                </span>

                                <h2>
                                    Assigned Book
                                </h2>

                            </div>

                        </div>


                        <div class="book-card">

                            <div class="book-info">

                                <h3>
                                    {{ $assignment->book->title }}
                                </h3>


                                {{-- Authors --}}

                                @if (
                                    $assignment
                                        ->book
                                        ->authors
                                        ->isNotEmpty()
                                )

                                    <p class="book-author">

                                        By

                                        {{
                                            $assignment
                                                ->book
                                                ->authors
                                                ->pluck('name')
                                                ->join(', ')
                                        }}

                                    </p>

                                @endif


                                {{-- Publisher --}}

                                @if ($assignment->book->publisher)

                                    <p class="book-publisher">

                                        Published by

                                        {{
                                            $assignment
                                                ->book
                                                ->publisher
                                                ->name
                                        }}

                                    </p>

                                @endif


                                {{-- Reading Range --}}

                                @if (
                                    $assignment->start_page
                                    || $assignment->end_page
                                )

                                    <div class="reading-range">

                                        <span>
                                            Required Reading
                                        </span>

                                        <strong>

                                            @if (
                                                $assignment->start_page
                                                && $assignment->end_page
                                            )

                                                Pages

                                                {{
                                                    $assignment
                                                        ->start_page
                                                }}

                                                –

                                                {{
                                                    $assignment
                                                        ->end_page
                                                }}

                                            @elseif (
                                                $assignment->start_page
                                            )

                                                Start from page

                                                {{
                                                    $assignment
                                                        ->start_page
                                                }}

                                            @elseif (
                                                $assignment->end_page
                                            )

                                                Read through page

                                                {{
                                                    $assignment
                                                        ->end_page
                                                }}

                                            @endif

                                        </strong>

                                    </div>

                                @endif

                            </div>


                            <div class="book-actions">

                                @if ($hasStarted)

                                    <a
                                        href="{{ route(
                                            'school.library.show',
                                            $assignment->book
                                        ) }}"
                                        class="btn btn-primary"
                                    >
                                        Open Book
                                    </a>

                                @else

                                    <span class="availability-note">

                                        Reading becomes available

                                        {{
                                            $assignment
                                                ->starts_at
                                                ->format(
                                                    'd M Y, H:i'
                                                )
                                        }}.

                                    </span>

                                @endif

                            </div>

                        </div>

                    </section>

                @else

                    <section class="panel notice-panel">

                        <h2>
                            No book attached
                        </h2>

                        <p>
                            This assignment does not currently have
                            an assigned reading resource.
                        </p>

                    </section>

                @endif


                {{-- ========================================================
                    Instructions
                ========================================================= --}}

                <section class="panel">

                    <div class="panel-heading">

                        <div>

                            <span class="section-label">
                                Assignment
                            </span>

                            <h2>
                                Instructions
                            </h2>

                        </div>

                    </div>


                    <div class="instructions">

                        @if ($assignment->instructions)

                            {!! nl2br(
                                e(
                                    $assignment
                                        ->instructions
                                )
                            ) !!}

                        @else

                            <p class="muted">
                                No additional instructions were provided.
                            </p>

                        @endif

                    </div>

                </section>


                {{-- ========================================================
                    Upcoming Assignment Notice
                ========================================================= --}}

                @if (! $hasStarted)

                    <section class="panel notice-panel">

                        <h2>
                            This assignment is not available yet
                        </h2>

                        <p>

                            You will be able to work on this
                            assignment from

                            <strong>

                                {{
                                    $assignment
                                        ->starts_at
                                        ->format(
                                            'd M Y, H:i'
                                        )
                                }}

                            </strong>.

                        </p>

                    </section>

                @endif


                {{-- ========================================================
                    Student Response
                ========================================================= --}}

                @if ($hasStarted)

                    <section class="panel">

                        <div class="panel-heading">

                            <div>

                                <span class="section-label">
                                    Your Work
                                </span>

                                <h2>
                                    Assignment Response
                                </h2>

                            </div>


                            @if ($submissionStatus === 'draft')

                                <span class="draft-indicator">
                                    Draft saved
                                </span>

                            @endif

                        </div>


                        {{-- =================================================
                            Submitted / Graded State
                        ================================================== --}}

                        @if ($isSubmitted)

                            <div class="submitted-response">

                                @if (
                                    filled(
                                        $submission?->response
                                    )
                                )

                                    {!! nl2br(
                                        e(
                                            $submission
                                                ->response
                                        )
                                    ) !!}

                                @else

                                    <span class="muted">
                                        No written response was recorded.
                                    </span>

                                @endif

                            </div>


                            <div class="submission-meta">

                                <span>

                                    @if (
                                        $submission
                                            ?->submitted_at
                                    )

                                        Submitted

                                        {{
                                            $submission
                                                ->submitted_at
                                                ->diffForHumans()
                                        }}

                                    @else

                                        Submission recorded

                                    @endif

                                </span>


                                @if (
                                    $submissionStatus
                                    === 'late'
                                )

                                    <span class="late-note">
                                        Submitted after deadline
                                    </span>

                                @elseif (
                                    $submissionStatus
                                    === 'graded'
                                )

                                    <span class="graded-note">
                                        Graded
                                    </span>

                                @endif

                            </div>


                        {{-- =================================================
                            Editable State
                        ================================================== --}}

                        @elseif ($canSaveDraft || $canSubmit)


                            {{-- =================================================
                                Shared Response Editor
                            ================================================== --}}

                            <label
                                for="assignment-response"
                                class="response-label"
                            >
                                Your response
                            </label>


                            <textarea
                                id="assignment-response"
                                rows="18"
                                maxlength="100000"
                                placeholder="Write your assignment response here..."
                                aria-describedby="response-help"
                            >{{ $currentResponse }}</textarea>


                            <div class="response-counter">

                                <span id="response-help">

                                    You may write up to
                                    100,000 characters.

                                </span>

                                <span>

                                    <span id="response-character-count">
                                        0
                                    </span>

                                    / 100,000

                                </span>

                            </div>


                            <div class="response-footer">

                                <div>

                                    <p>
                                        Save your work as a draft
                                        and continue later.
                                    </p>

                                    @if ($isPastDue)

                                        <p class="late-warning">

                                            The deadline has passed.
                                            Your final submission will
                                            be marked as late.

                                        </p>

                                    @endif

                                </div>


                                <div class="response-actions">


                                    {{-- =====================================
                                        Save Draft Form
                                    ====================================== --}}

                                    @if ($canSaveDraft)

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'student.assignments.draft',
                                                $assignment
                                            ) }}"
                                            class="response-action-form"
                                            data-response-form
                                        >

                                            @csrf
                                            @method('PUT')


                                            <input
                                                type="hidden"
                                                name="response"
                                                value=""
                                            >


                                            <button
                                                type="submit"
                                                class="btn btn-secondary"
                                            >
                                                Save Draft
                                            </button>

                                        </form>

                                    @endif


                                    {{-- =====================================
                                        Final Submission Form
                                    ====================================== --}}

                                    @if ($canSubmit)

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'student.assignments.submit',
                                                $assignment
                                            ) }}"
                                            class="response-action-form"
                                            data-response-form
                                            data-final-submission
                                        >

                                            @csrf


                                            <input
                                                type="hidden"
                                                name="response"
                                                value=""
                                            >


                                            <button
                                                type="submit"
                                                class="btn btn-primary"
                                            >

                                                @if ($isPastDue)

                                                    Submit Late

                                                @else

                                                    Submit Assignment

                                                @endif

                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </div>


                        {{-- =================================================
                            Not Accepting Work
                        ================================================== --}}

                        @else

                            <div class="empty-response-state">

                                <strong>
                                    Assignment unavailable
                                </strong>

                                <p>
                                    This assignment is not currently
                                    accepting student work.
                                </p>

                            </div>

                        @endif

                    </section>

                @endif


                {{-- ========================================================
                    Grade & Feedback
                ========================================================= --}}

                @if ($isGraded)

                    <section class="panel grade-panel">

                        <div class="panel-heading">

                            <div>

                                <span class="section-label">
                                    Assessment
                                </span>

                                <h2>
                                    Result & Feedback
                                </h2>

                            </div>

                        </div>


                        <div class="grade-summary">


                            {{-- Score --}}

                            <div class="score-box">

                                <span>
                                    Score
                                </span>

                                <strong>

                                    {{
                                        $submission->score
                                        ?? '—'
                                    }}


                                    @if ($assignment->total_marks)

                                        <small>

                                            /

                                            {{
                                                $assignment
                                                    ->total_marks
                                            }}

                                        </small>

                                    @endif

                                </strong>

                            </div>


                            {{-- Percentage --}}

                            @if ($percentage !== null)

                                <div class="score-box">

                                    <span>
                                        Percentage
                                    </span>

                                    <strong>
                                        {{ $percentage }}%
                                    </strong>

                                </div>

                            @endif


                            {{-- Status --}}

                            <div class="score-box">

                                <span>
                                    Status
                                </span>

                                <strong class="score-status">
                                    Graded
                                </strong>

                            </div>

                        </div>


                        {{-- Teacher Feedback --}}

                        <div class="feedback-block">

                            <h3>
                                Teacher Feedback
                            </h3>


                            @if ($submission->feedback)

                                <div class="feedback-text">

                                    {!! nl2br(
                                        e(
                                            $submission
                                                ->feedback
                                        )
                                    ) !!}

                                </div>

                            @else

                                <p class="muted">
                                    No written feedback was provided.
                                </p>

                            @endif


                            @if ($submission->grader)

                                <small>

                                    Graded by

                                    <strong>

                                        {{
                                            $submission
                                                ->grader
                                                ->name
                                        }}

                                    </strong>

                                </small>

                            @endif

                        </div>

                    </section>

                @endif

            </main>


            {{-- ============================================================
                Sidebar
            ============================================================= --}}

            <aside class="sidebar">


                {{-- ========================================================
                    Assignment Details
                ========================================================= --}}

                <section class="sidebar-panel">

                    <h3>
                        Assignment Details
                    </h3>


                    <dl>


                        {{-- Status --}}

                        <div>

                            <dt>
                                Status
                            </dt>

                            <dd>
                                {{ $displayStatus }}
                            </dd>

                        </div>


                        {{-- Class --}}

                        @if ($assignment->schoolClass)

                            <div>

                                <dt>
                                    Class
                                </dt>

                                <dd>

                                    {{
                                        $assignment
                                            ->schoolClass
                                            ->name
                                    }}

                                </dd>

                            </div>

                        @endif


                        {{-- Teacher --}}

                        @if ($assignment->creator)

                            <div>

                                <dt>
                                    Teacher
                                </dt>

                                <dd>

                                    {{
                                        $assignment
                                            ->creator
                                            ->name
                                    }}

                                </dd>

                            </div>

                        @endif


                        {{-- Book --}}

                        @if ($assignment->book)

                            <div>

                                <dt>
                                    Book
                                </dt>

                                <dd>

                                    {{
                                        $assignment
                                            ->book
                                            ->title
                                    }}

                                </dd>

                            </div>

                        @endif


                        {{-- Available --}}

                        <div>

                            <dt>
                                Available
                            </dt>

                            <dd>

                                {{
                                    $assignment->starts_at
                                        ? $assignment
                                            ->starts_at
                                            ->format(
                                                'd M Y, H:i'
                                            )
                                        : 'Immediately'
                                }}

                            </dd>

                        </div>


                        {{-- Due Date --}}

                        <div>

                            <dt>
                                Due
                            </dt>

                            <dd>

                                {{
                                    $assignment->due_at
                                        ? $assignment
                                            ->due_at
                                            ->format(
                                                'd M Y, H:i'
                                            )
                                        : 'No deadline'
                                }}

                            </dd>

                        </div>


                        {{-- Reading Range --}}

                        @if (
                            $assignment->start_page
                            || $assignment->end_page
                        )

                            <div>

                                <dt>
                                    Reading Range
                                </dt>

                                <dd>

                                    @if (
                                        $assignment->start_page
                                        && $assignment->end_page
                                    )

                                        {{
                                            $assignment
                                                ->start_page
                                        }}

                                        –

                                        {{
                                            $assignment
                                                ->end_page
                                        }}

                                    @elseif (
                                        $assignment->start_page
                                    )

                                        From page

                                        {{
                                            $assignment
                                                ->start_page
                                        }}

                                    @else

                                        Through page

                                        {{
                                            $assignment
                                                ->end_page
                                        }}

                                    @endif

                                </dd>

                            </div>

                        @endif


                        {{-- Marks --}}

                        @if ($assignment->total_marks)

                            <div>

                                <dt>
                                    Total Marks
                                </dt>

                                <dd>
                                    {{ $assignment->total_marks }}
                                </dd>

                            </div>

                        @endif


                        {{-- Score --}}

                        @if ($isGraded)

                            <div>

                                <dt>
                                    Your Score
                                </dt>

                                <dd>

                                    {{
                                        $submission->score
                                        ?? '—'
                                    }}

                                    @if ($assignment->total_marks)

                                        /

                                        {{
                                            $assignment
                                                ->total_marks
                                        }}

                                    @endif

                                </dd>

                            </div>

                        @endif

                    </dl>

                </section>


                {{-- ========================================================
                    Book Shortcut
                ========================================================= --}}

                @if (
                    $assignment->book
                    && $hasStarted
                )

                    <section class="sidebar-panel">

                        <h3>
                            Reading
                        </h3>

                        <p class="sidebar-description">
                            Open the assigned book and complete
                            the required reading before submitting.
                        </p>

                        <a
                            href="{{ route(
                                'school.library.show',
                                $assignment->book
                            ) }}"
                            class="btn btn-secondary btn-full"
                        >
                            Open Assigned Book
                        </a>

                    </section>

                @endif


                {{-- ========================================================
                    Deadline Passed
                ========================================================= --}}

                @if (
                    $isPastDue
                    && ! $isSubmitted
                )

                    <section class="sidebar-panel warning-panel">

                        <h3>
                            Deadline Passed
                        </h3>

                        <p>

                            This assignment is overdue.

                            Late submissions are currently
                            permitted and will automatically
                            be marked as late.

                        </p>

                    </section>

                @endif


                {{-- ========================================================
                    Upcoming
                ========================================================= --}}

                @if (! $hasStarted)

                    <section class="sidebar-panel info-panel">

                        <h3>
                            Upcoming Assignment
                        </h3>

                        <p>

                            This assignment will become available

                            <strong>

                                {{
                                    $assignment
                                        ->starts_at
                                        ->diffForHumans()
                                }}

                            </strong>.

                        </p>

                    </section>

                @endif


                {{-- ========================================================
                    Submission Confirmation
                ========================================================= --}}

                @if (
                    $isSubmitted
                    && ! $isGraded
                )

                    <section class="sidebar-panel success-panel">

                        <h3>
                            Work Submitted
                        </h3>

                        <p>

                            Your response has been submitted
                            successfully and is awaiting review
                            by your teacher.

                        </p>

                    </section>

                @endif


                {{-- ========================================================
                    Graded Confirmation
                ========================================================= --}}

                @if ($isGraded)

                    <section class="sidebar-panel grade-sidebar">

                        <h3>
                            Assignment Graded
                        </h3>

                        <p>
                            Your teacher has reviewed this
                            assignment. Your score and feedback
                            are available on this page.
                        </p>

                    </section>

                @endif

            </aside>

        </div>

    </div>

</div>


{{-- =========================================================================
    Response Handling Script
============================================================================ --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const editor =
        document.getElementById(
            'assignment-response'
        );

    if (! editor) {
        return;
    }


    const count =
        document.getElementById(
            'response-character-count'
        );


    const updateCount = function () {

        if (count) {
            count.textContent =
                editor.value.length;
        }

    };


    updateCount();


    editor.addEventListener(
        'input',
        updateCount
    );


    /*
    |--------------------------------------------------------------------------
    | Copy editor value into whichever form is submitted
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll(
            '[data-response-form]'
        )
        .forEach(function (form) {

            form.addEventListener(
                'submit',
                function (event) {

                    const hiddenResponse =
                        form.querySelector(
                            'input[name="response"]'
                        );


                    if (hiddenResponse) {

                        hiddenResponse.value =
                            editor.value;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Final Submission Confirmation
                    |--------------------------------------------------------------------------
                    */

                    if (
                        form.hasAttribute(
                            'data-final-submission'
                        )
                    ) {

                        if (
                            editor
                                .value
                                .trim()
                                .length === 0
                        ) {

                            event.preventDefault();

                            alert(
                                'Please enter your assignment response before submitting.'
                            );

                            editor.focus();

                            return;
                        }


                        const confirmed =
                            window.confirm(
                                'Submit this assignment? You will not be able to edit it after submission.'
                            );


                        if (! confirmed) {

                            event.preventDefault();

                            return;
                        }

                    }

                }
            );

        });

});
</script>


<style>

/* ==========================================================================
   Page
========================================================================== */

.assignment-page {
    min-height: 100vh;
    padding: 28px 20px 64px;
    background: #f7f9fc;
    color: #172033;
}

.page-shell {
    width: min(1180px, 100%);
    margin: 0 auto;
}


/* ==========================================================================
   Navigation
========================================================================== */

.top-navigation {
    margin-bottom: 18px;
}

.back-link {
    color: #475467;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
}

.back-link:hover {
    color: #002b5c;
}


/* ==========================================================================
   Assignment Header
========================================================================== */

.assignment-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 30px;
    background: #ffffff;
    border: 1px solid #e5eaf0;
    padding: 26px;
    margin-bottom: 16px;
}

.assignment-header-main {
    min-width: 0;
}

.assignment-header h1 {
    margin: 10px 0 12px;
    color: #002b5c;
    font-size: clamp(
        27px,
        4vw,
        39px
    );
    line-height: 1.15;
    overflow-wrap: anywhere;
}

.header-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 22px;
    color: #667085;
    font-size: 14px;
}

.header-meta strong {
    color: #344054;
}


/* ==========================================================================
   Status
========================================================================== */

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 5px 9px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .03em;
    text-transform: uppercase;
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


/* ==========================================================================
   Marks
========================================================================== */

.marks-card {
    flex: 0 0 auto;
    min-width: 130px;
    text-align: right;
}

.marks-card span {
    display: block;
    color: #667085;
    font-size: 12px;
}

.marks-card strong {
    display: block;
    margin-top: 5px;
    color: #002b5c;
    font-size: 34px;
}


/* ==========================================================================
   Timeline
========================================================================== */

.timeline-grid {
    display: grid;
    grid-template-columns:
        repeat(
            auto-fit,
            minmax(
                180px,
                1fr
            )
        );
    gap: 12px;
    margin-bottom: 16px;
}

.timeline-card {
    background: #ffffff;
    border: 1px solid #e5eaf0;
    padding: 16px;
}

.timeline-label {
    display: block;
    margin-bottom: 5px;
    color: #667085;
    font-size: 11px;
    letter-spacing: .05em;
    text-transform: uppercase;
}

.timeline-card strong {
    color: #344054;
    font-size: 14px;
}

.danger-text {
    color: #b42318 !important;
}


/* ==========================================================================
   Main Grid
========================================================================== */

.content-grid {
    display: grid;
    grid-template-columns:
        minmax(
            0,
            1fr
        )
        290px;
    gap: 18px;
    align-items: start;
}

.main-column {
    display: grid;
    gap: 16px;
}


/* ==========================================================================
   Panels
========================================================================== */

.panel,
.sidebar-panel {
    background: #ffffff;
    border: 1px solid #e5eaf0;
    padding: 24px;
}

.panel-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 18px;
}

.section-label {
    display: block;
    margin-bottom: 4px;
    color: #067a89;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.panel-heading h2 {
    margin: 0;
    color: #002b5c;
    font-size: 20px;
}


/* ==========================================================================
   Book
========================================================================== */

.book-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
}

.book-info {
    min-width: 0;
}

.book-info h3 {
    margin: 0 0 5px;
    color: #002b5c;
    font-size: 20px;
    overflow-wrap: anywhere;
}

.book-author,
.book-publisher {
    margin: 4px 0 0;
    color: #667085;
}

.book-publisher {
    font-size: 13px;
}

.book-actions {
    flex: 0 0 auto;
}

.reading-range {
    margin-top: 14px;
}

.reading-range span {
    display: block;
    color: #667085;
    font-size: 11px;
    text-transform: uppercase;
}

.reading-range strong {
    display: block;
    margin-top: 4px;
    color: #344054;
}


/* ==========================================================================
   Instructions
========================================================================== */

.instructions {
    color: #344054;
    line-height: 1.75;
    overflow-wrap: anywhere;
}

.instructions p {
    margin: 0;
}


/* ==========================================================================
   Response Editor
========================================================================== */

.response-label {
    display: block;
    margin-bottom: 8px;
    color: #344054;
    font-weight: 600;
}

#assignment-response {
    display: block;
    width: 100%;
    min-height: 320px;
    resize: vertical;
    border: 1px solid #d0d5dd;
    background: #ffffff;
    box-sizing: border-box;
    padding: 15px;
    color: #172033;
    font: inherit;
    line-height: 1.6;
}

#assignment-response:focus {
    outline: 2px solid #0097a7;
    outline-offset: 1px;
    border-color: #0097a7;
}

.response-counter {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin-top: 7px;
    color: #667085;
    font-size: 11px;
}

.response-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-top: 16px;
}

.response-footer p {
    margin: 0;
    color: #667085;
    font-size: 13px;
}

.response-actions {
    display: flex;
    gap: 9px;
    flex-shrink: 0;
}

.response-action-form {
    margin: 0;
}

.draft-indicator {
    color: #475467;
    font-size: 12px;
    font-weight: 600;
}

.late-warning {
    margin-top: 5px !important;
    color: #b42318 !important;
}


/* ==========================================================================
   Submitted Work
========================================================================== */

.submitted-response {
    min-height: 70px;
    background: #f8fafc;
    border: 1px solid #eaecf0;
    padding: 18px;
    color: #344054;
    line-height: 1.75;
    overflow-wrap: anywhere;
}

.submission-meta {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    margin-top: 12px;
    color: #667085;
    font-size: 12px;
}

.late-note {
    color: #b42318;
    font-weight: 600;
}

.graded-note {
    color: #8b6500;
    font-weight: 600;
}

.empty-response-state {
    background: #f8fafc;
    border: 1px solid #eaecf0;
    padding: 18px;
}

.empty-response-state strong {
    color: #344054;
}

.empty-response-state p {
    margin: 5px 0 0;
    color: #667085;
}


/* ==========================================================================
   Grade
========================================================================== */

.grade-panel {
    border-top: 3px solid #d8a21b;
}

.grade-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 22px;
}

.score-box {
    min-width: 130px;
    border: 1px solid #e5eaf0;
    padding: 16px;
}

.score-box span {
    display: block;
    color: #667085;
    font-size: 11px;
    text-transform: uppercase;
}

.score-box strong {
    display: block;
    margin-top: 5px;
    color: #002b5c;
    font-size: 27px;
}

.score-box small {
    color: #667085;
    font-size: 15px;
}

.score-status {
    font-size: 18px !important;
}

.feedback-block {
    border-top: 1px solid #eaecf0;
    padding-top: 18px;
}

.feedback-block h3 {
    margin: 0 0 10px;
    color: #002b5c;
}

.feedback-text {
    margin-bottom: 14px;
    color: #344054;
    line-height: 1.7;
    overflow-wrap: anywhere;
}

.feedback-block small {
    color: #667085;
}


/* ==========================================================================
   Sidebar
========================================================================== */

.sidebar {
    display: grid;
    gap: 14px;
}

.sidebar-panel h3 {
    margin: 0 0 16px;
    color: #002b5c;
    font-size: 17px;
}

.sidebar-description {
    margin: 0 0 14px;
    color: #667085;
    line-height: 1.6;
}

.sidebar-panel dl {
    margin: 0;
}

.sidebar-panel dl div {
    border-top: 1px solid #eaecf0;
    padding: 12px 0;
}

.sidebar-panel dl div:first-child {
    border-top: 0;
    padding-top: 0;
}

.sidebar-panel dt {
    color: #667085;
    font-size: 11px;
    text-transform: uppercase;
}

.sidebar-panel dd {
    margin: 4px 0 0;
    color: #344054;
    font-weight: 600;
    overflow-wrap: anywhere;
}


/* ==========================================================================
   Notices
========================================================================== */

.warning-panel {
    border-color: #fecdca;
    background: #fff8f7;
}

.warning-panel p {
    margin-bottom: 0;
    color: #912018;
    line-height: 1.6;
}

.info-panel {
    border-color: #b2ddff;
    background: #f5fbff;
}

.info-panel p {
    margin-bottom: 0;
    color: #175cd3;
    line-height: 1.6;
}

.success-panel {
    border-color: #abefc6;
    background: #f6fef9;
}

.success-panel p {
    margin-bottom: 0;
    color: #067647;
    line-height: 1.6;
}

.grade-sidebar {
    border-color: #f5df9a;
    background: #fffaf0;
}

.grade-sidebar p {
    margin-bottom: 0;
    color: #725300;
    line-height: 1.6;
}

.notice-panel {
    background: #f7f9fc;
}

.notice-panel h2 {
    margin: 0 0 8px;
    color: #002b5c;
    font-size: 18px;
}

.notice-panel p {
    margin: 0;
    color: #475467;
    line-height: 1.6;
}


/* ==========================================================================
   Buttons
========================================================================== */

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 9px 16px;
    border: 1px solid transparent;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
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
    border-color: #cbd5e1;
    color: #002b5c;
}

.btn-secondary:hover {
    background: #f8fafc;
}

.btn-full {
    width: 100%;
}


/* ==========================================================================
   Alerts
========================================================================== */

.alert {
    border: 1px solid;
    padding: 14px 16px;
    margin-bottom: 16px;
}

.alert-success {
    background: #ecfdf3;
    border-color: #abefc6;
    color: #067647;
}

.alert-error {
    background: #fef3f2;
    border-color: #fecdca;
    color: #b42318;
}

.alert-error ul {
    margin: 8px 0 0;
    padding-left: 20px;
}


/* ==========================================================================
   Utility
========================================================================== */

.availability-note,
.muted {
    color: #667085;
}


/* ==========================================================================
   Tablet
========================================================================== */

@media (max-width: 900px) {

    .content-grid {
        grid-template-columns: 1fr;
    }

    .sidebar {
        grid-row: auto;
    }

}


/* ==========================================================================
   Mobile
========================================================================== */

@media (max-width: 650px) {

    .assignment-page {
        padding: 20px 13px 48px;
    }

    .assignment-header {
        flex-direction: column;
    }

    .marks-card {
        text-align: left;
    }

    .book-card {
        flex-direction: column;
        align-items: flex-start;
    }

    .book-actions {
        width: 100%;
    }

    .book-actions .btn {
        width: 100%;
    }

    .response-counter {
        flex-direction: column;
        gap: 3px;
    }

    .response-footer {
        flex-direction: column;
        align-items: stretch;
    }

    .response-actions {
        flex-direction: column;
    }

    .response-action-form {
        width: 100%;
    }

    .response-actions .btn {
        width: 100%;
    }

    .submission-meta {
        flex-direction: column;
    }

    .grade-summary {
        flex-direction: column;
    }

    .score-box {
        width: 100%;
        box-sizing: border-box;
    }

}


/* ==========================================================================
   Reduced Motion
========================================================================== */

@media (
    prefers-reduced-motion:
    reduce
) {

    *,
    *::before,
    *::after {
        scroll-behavior: auto !important;
        transition: none !important;
    }

}

</style>

</x-layouts.dashboard>