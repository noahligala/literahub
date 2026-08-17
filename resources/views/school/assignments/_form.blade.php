<div class="form-grid">

    <div class="form-group">

        <label>Assignment Title</label>

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

        <label>Class</label>

        <select
            name="school_class_id"
            required
        >

            <option value="">
                Select Class
            </option>

            @foreach($classes as $class)

                <option
                    value="{{ $class->id }}"
                    @selected(
                        old(
                            'school_class_id',
                            $assignment
                                ->school_class_id
                                ?? null
                        ) == $class->id
                    )
                >
                    {{ $class->name }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="form-group">

        <label>Due Date</label>

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

        <label>Status</label>

        <select name="status">

            @foreach([
                'draft' => 'Draft',
                'published' => 'Published',
                'closed' => 'Closed',
            ] as $value => $label)

                <option
                    value="{{ $value }}"
                    @selected(
                        old(
                            'status',
                            $assignment->status
                                ?? 'draft'
                        ) === $value
                    )
                >
                    {{ $label }}
                </option>

            @endforeach

        </select>

    </div>

</div>

<div class="form-group">

    <label>Instructions</label>

    <textarea
        name="instructions"
        placeholder="Describe the reading task, questions or assessment..."
    >{{ old(
        'instructions',
        $assignment->instructions ?? ''
    ) }}</textarea>

</div>