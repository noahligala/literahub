<div class="form-grid">

    <div class="form-group">

        <label>
            Assignment Title
        </label>

        <input
            name="title"
            value="{{ old(
                'title',
                $assignment->title ?? ''
            ) }}"
            required
        >

    </div>


    <div class="form-group">

        <label>
            Class
        </label>

        <select
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
                            $assignment
                                ->school_class_id
                                ?? null
                        )
                        == $class->id
                    )
                >
                    {{ $class->name }}
                </option>

            @endforeach

        </select>

    </div>


    <div class="form-group">

        <label>
            Book
        </label>

        <select
            name="resource_id"
        >

            <option value="">
                Select Book
            </option>


            @foreach ($books as $book)

                <option
                    value="{{ $book->id }}"
                    @selected(
                        old(
                            'resource_id',
                            $assignment
                                ->resource_id
                                ?? null
                        )
                        == $book->id
                    )
                >
                    {{ $book->title }}

                    @if (
                        $book->authors->isNotEmpty()
                    )
                        —
                        {{ $book
                            ->authors
                            ->pluck('name')
                            ->join(', ')
                        }}
                    @endif
                </option>

            @endforeach

        </select>

        <small class="form-hint">
            Only books currently licensed to this school
            and allowed for teacher assignment are shown.
        </small>

    </div>


    <div class="form-group">

        <label>
            Available From
        </label>

        <input
            type="datetime-local"
            name="starts_at"
            value="{{ old(
                'starts_at',
                isset($assignment)
                    && $assignment->starts_at
                        ? $assignment
                            ->starts_at
                            ->format(
                                'Y-m-d\TH:i'
                            )
                        : ''
            ) }}"
        >

    </div>


    <div class="form-group">

        <label>
            Due Date
        </label>

        <input
            type="datetime-local"
            name="due_at"
            value="{{ old(
                'due_at',
                isset($assignment)
                    && $assignment->due_at
                        ? $assignment
                            ->due_at
                            ->format(
                                'Y-m-d\TH:i'
                            )
                        : ''
            ) }}"
        >

    </div>


    <div class="form-group">

        <label>
            Total Marks
        </label>

        <input
            type="number"
            name="total_marks"
            min="1"
            max="1000"
            value="{{ old(
                'total_marks',
                $assignment
                    ->total_marks
                    ?? ''
            ) }}"
            placeholder="e.g. 20"
        >

    </div>


    <div class="form-group">

        <label>
            Start Page
        </label>

        <input
            type="number"
            name="start_page"
            min="1"
            value="{{ old(
                'start_page',
                $assignment
                    ->start_page
                    ?? ''
            ) }}"
            placeholder="Optional"
        >

    </div>


    <div class="form-group">

        <label>
            End Page
        </label>

        <input
            type="number"
            name="end_page"
            min="1"
            value="{{ old(
                'end_page',
                $assignment
                    ->end_page
                    ?? ''
            ) }}"
            placeholder="Optional"
        >

    </div>


    <div class="form-group">

        <label>
            Status
        </label>

        <select
            name="status"
            required
        >

            @foreach ([
                'draft' =>
                    'Draft',

                'published' =>
                    'Published',

                'closed' =>
                    'Closed',
            ] as $value => $label)

                <option
                    value="{{ $value }}"
                    @selected(
                        old(
                            'status',
                            $assignment
                                ->status
                                ?? 'draft'
                        )
                        === $value
                    )
                >
                    {{ $label }}
                </option>

            @endforeach

        </select>

    </div>

</div>


<div class="form-group">

    <label>
        Instructions
    </label>

    <textarea
        name="instructions"
        placeholder="Describe the reading task, questions or assessment..."
    >{{ old(
        'instructions',
        $assignment->instructions ?? ''
    ) }}</textarea>

</div>