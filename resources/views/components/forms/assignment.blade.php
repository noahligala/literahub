@props([
    'assignment' => null,
    'classes' => collect(),
    'resources' => collect(),
])

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

            @foreach($classes as $class)

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


    <div class="form-group">

        <label for="resource_id">
            Literature Resource
        </label>

        <select
            id="resource_id"
            name="resource_id"
        >

            <option value="">
                No Resource Attached
            </option>

            @foreach($resources as $resource)

                <option
                    value="{{ $resource->id }}"
                    @selected(
                        old(
                            'resource_id',
                            $assignment?->resource_id
                        ) == $resource->id
                    )
                >
                    {{ $resource->title }}
                </option>

            @endforeach

        </select>

    </div>


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

    </div>


    <div class="form-group">

        <label for="status">
            Status
        </label>

        <select
            id="status"
            name="status"
            required
        >

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
                            $assignment?->status
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

    <label for="instructions">
        Instructions
    </label>

    <textarea
        id="instructions"
        name="instructions"
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