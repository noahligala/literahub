@props([

    'assignment' => null,

    'classes' => collect(),

    'books' => collect(),

])


{{-- =========================================================================
    Core Assignment Fields
============================================================================ --}}

<div class="form-grid">

    <div class="form-group">

        <label for="title">
            Assignment Title
        </label>

        <input
            id="title"
            name="title"
            value="{{ old(
                'title',
                $assignment?->title
            ) }}"
            placeholder="e.g. Chapter 4 Reading"
            required
        >

        @error('title')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    <div class="form-group">

        <label for="school_class_id">
            Class
        </label>

        <select
            id="school_class_id"
            name="school_class_id"
            required
        >

            <option value="">
                Select Class
            </option>

            @foreach ($classes as $class)

                <option
                    value="{{ $class->id }}"
                    @selected(
                        old(
                            'school_class_id',
                            $assignment?->school_class_id
                        ) == $class->id
                    )
                >
                    {{ $class->name }}
                </option>

            @endforeach

        </select>

        @error('school_class_id')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    {{-- =====================================================================
        Attached Book
    ====================================================================== --}}

    <div class="form-group">

        <label for="resource_id">
            Assigned Book
        </label>

        <select
            id="resource_id"
            name="resource_id"
        >

            <option value="">
                No Book Attached
            </option>

            @foreach ($books as $book)

                <option
                    value="{{ $book->id }}"
                    @selected(
                        old(
                            'resource_id',
                            $assignment?->resource_id
                        ) == $book->id
                    )
                >

                    {{ $book->title }}

                    @if (
                        isset($book->authors)
                        && $book->authors->isNotEmpty()
                    )

                        — {{ $book->authors->pluck('name')->join(', ') }}

                    @endif

                </option>

            @endforeach

        </select>


        <small class="field-hint">
            Only books licensed to the school and permitted
            for teacher assignment should appear here.
        </small>


        @error('resource_id')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    {{-- =====================================================================
        Start Date
    ====================================================================== --}}

    <div class="form-group">

        <label for="starts_at">
            Available From
        </label>

        <input
            id="starts_at"
            type="datetime-local"
            name="starts_at"
            value="{{ old(
                'starts_at',
                $assignment?->starts_at
                    ? $assignment
                        ->starts_at
                        ->format('Y-m-d\TH:i')
                    : ''
            ) }}"
        >

        <small class="field-hint">
            Leave blank to make the assignment available immediately.
        </small>

        @error('starts_at')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    {{-- =====================================================================
        Due Date
    ====================================================================== --}}

    <div class="form-group">

        <label for="due_at">
            Due Date & Time
        </label>

        <input
            id="due_at"
            type="datetime-local"
            name="due_at"
            value="{{ old(
                'due_at',
                $assignment?->due_at
                    ? $assignment
                        ->due_at
                        ->format('Y-m-d\TH:i')
                    : ''
            ) }}"
        >

        @error('due_at')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    {{-- =====================================================================
        Status
    ====================================================================== --}}

    <div class="form-group">

        <label for="status">
            Status
        </label>

        <select
            id="status"
            name="status"
            required
        >

            @foreach ([
                'draft' => 'Draft',
                'published' => 'Published',
                'closed' => 'Closed',
            ] as $value => $label)

                <option
                    value="{{ $value }}"
                    @selected(
                        old(
                            'status',
                            $assignment?->status
                                ?? 'draft'
                        ) === $value
                    )
                >
                    {{ $label }}
                </option>

            @endforeach

        </select>

        @error('status')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>

</div>


{{-- =========================================================================
    Reading Range & Marks
============================================================================ --}}

<div class="form-grid">

    <div class="form-group">

        <label for="start_page">
            Start Page
        </label>

        <input
            id="start_page"
            type="number"
            name="start_page"
            min="1"
            value="{{ old(
                'start_page',
                $assignment?->start_page
            ) }}"
            placeholder="e.g. 10"
        >

        @error('start_page')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    <div class="form-group">

        <label for="end_page">
            End Page
        </label>

        <input
            id="end_page"
            type="number"
            name="end_page"
            min="1"
            value="{{ old(
                'end_page',
                $assignment?->end_page
            ) }}"
            placeholder="e.g. 35"
        >

        @error('end_page')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    <div class="form-group">

        <label for="total_marks">
            Total Marks
        </label>

        <input
            id="total_marks"
            type="number"
            name="total_marks"
            min="0"
            value="{{ old(
                'total_marks',
                $assignment?->total_marks
            ) }}"
            placeholder="e.g. 100"
        >

        @error('total_marks')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>

</div>


{{-- =========================================================================
    Late Submission Policy
============================================================================ --}}

<div class="section-divider">

    <div>

        <span class="form-section-eyebrow">
            Submission Rules
        </span>

        <h3>
            Late Submission Policy
        </h3>

        <p>
            Choose what should happen when a learner submits
            after the assignment deadline.
        </p>

    </div>

</div>


<div class="form-grid">

    <div class="form-group">

        <label for="late_submission_policy">
            Late Submissions
        </label>

        <select
            id="late_submission_policy"
            name="late_submission_policy"
            required
        >

            <option
                value="allow"
                @selected(
                    old(
                        'late_submission_policy',
                        $assignment?->late_submission_policy
                            ?? 'allow'
                    ) === 'allow'
                )
            >
                Accept late submissions
            </option>


            <option
                value="allow_with_penalty"
                @selected(
                    old(
                        'late_submission_policy',
                        $assignment?->late_submission_policy
                    ) === 'allow_with_penalty'
                )
            >
                Accept late submissions with penalty
            </option>


            <option
                value="reject"
                @selected(
                    old(
                        'late_submission_policy',
                        $assignment?->late_submission_policy
                    ) === 'reject'
                )
            >
                Reject late submissions
            </option>

        </select>

        @error('late_submission_policy')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>

</div>


<div
    id="late-penalty-settings"
    class="form-grid penalty-settings"
>

    <div class="form-group">

        <label for="late_penalty_type">
            Penalty Type
        </label>

        <select
            id="late_penalty_type"
            name="late_penalty_type"
        >

            <option
                value="percentage"
                @selected(
                    old(
                        'late_penalty_type',
                        $assignment?->late_penalty_type
                            ?? 'percentage'
                    ) === 'percentage'
                )
            >
                Percentage of awarded marks
            </option>


            <option
                value="fixed"
                @selected(
                    old(
                        'late_penalty_type',
                        $assignment?->late_penalty_type
                    ) === 'fixed'
                )
            >
                Fixed marks deduction
            </option>

        </select>

        @error('late_penalty_type')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    <div class="form-group">

        <label for="late_penalty_value">
            Penalty Value
        </label>

        <input
            id="late_penalty_value"
            type="number"
            name="late_penalty_value"
            min="0"
            step="0.01"
            value="{{ old(
                'late_penalty_value',
                $assignment?->late_penalty_value
            ) }}"
            placeholder="e.g. 10"
        >

        <small
            id="late-penalty-help"
            class="field-hint"
        >
            Example: 10 means a 10% deduction when percentage
            is selected, or 10 marks when fixed is selected.
        </small>

        @error('late_penalty_value')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>

</div>


{{-- =========================================================================
    Instructions
============================================================================ --}}

<div class="form-group">

    <label for="instructions">
        Instructions
    </label>

    <textarea
        id="instructions"
        name="instructions"
        rows="8"
        placeholder="Provide reading instructions, questions or submission requirements..."
    >{{ old(
        'instructions',
        $assignment?->instructions
    ) }}</textarea>

    @error('instructions')

        <div class="field-error">
            {{ $message }}
        </div>

    @enderror

</div>


{{-- =========================================================================
    Component Behaviour
============================================================================ --}}

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const policy =
            document.getElementById(
                'late_submission_policy'
            );

        const penaltySettings =
            document.getElementById(
                'late-penalty-settings'
            );

        const penaltyType =
            document.getElementById(
                'late_penalty_type'
            );

        const penaltyValue =
            document.getElementById(
                'late_penalty_value'
            );

        const penaltyHelp =
            document.getElementById(
                'late-penalty-help'
            );


        if (
            ! policy
            || ! penaltySettings
        ) {
            return;
        }


        const refreshPenaltyVisibility =
            function () {

                const enabled =
                    policy.value ===
                    'allow_with_penalty';

                penaltySettings.hidden =
                    ! enabled;


                if (penaltyType) {
                    penaltyType.disabled =
                        ! enabled;
                }


                if (penaltyValue) {
                    penaltyValue.disabled =
                        ! enabled;
                }

            };


        const refreshPenaltyHelp =
            function () {

                if (
                    ! penaltyType
                    || ! penaltyHelp
                ) {
                    return;
                }


                if (
                    penaltyType.value ===
                    'fixed'
                ) {

                    penaltyHelp.textContent =
                        'The specified number of marks will be deducted from a late submission.';

                } else {

                    penaltyHelp.textContent =
                        'The specified percentage will be deducted from the learner’s awarded marks.';

                }

            };


        policy.addEventListener(
            'change',
            refreshPenaltyVisibility
        );


        if (penaltyType) {

            penaltyType.addEventListener(
                'change',
                refreshPenaltyHelp
            );

        }


        refreshPenaltyVisibility();
        refreshPenaltyHelp();

    }
);
</script>


<style>

    .section-divider {
        margin:
            28px 0
            18px;

        padding-top:
            22px;

        border-top:
            1px solid
            var(
                --color-border,
                #e5eaf0
            );
    }


    .section-divider h3 {
        margin:
            4px 0
            5px;

        color:
            var(
                --color-text,
                #002b5c
            );

        font-size:
            .82rem;
    }


    .section-divider p {
        max-width:
            680px;

        margin:
            0;

        color:
            var(
                --color-text-muted,
                #667085
            );

        font-size:
            .55rem;

        line-height:
            1.6;
    }


    .form-section-eyebrow {
        color:
            var(
                --color-primary,
                #0097a7
            );

        font-size:
            .48rem;

        font-weight:
            800;

        text-transform:
            uppercase;

        letter-spacing:
            .06em;
    }


    .field-hint {
        display:
            block;

        margin-top:
            5px;

        color:
            var(
                --color-text-muted,
                #667085
            );

        font-size:
            .48rem;

        line-height:
            1.5;
    }


    .penalty-settings[hidden] {
        display:
            none !important;
    }

</style>