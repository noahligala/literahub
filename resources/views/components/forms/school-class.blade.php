@props([
    'class' => null,
])

<div class="form-grid">

    <div class="form-group">

        <label for="name">
            Class Name
        </label>

        <input
            id="name"
            name="name"
            value="{{ old(
                'name',
                $class?->name
            ) }}"
            placeholder="e.g. Form 3"
            required
        >

        @error('name')
            <div class="field-error">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="form-group">

        <label for="code">
            Class Code
        </label>

        <input
            id="code"
            name="code"
            value="{{ old(
                'code',
                $class?->code
            ) }}"
            placeholder="e.g. F3"
        >

    </div>


    <div class="form-group">

        <label for="level">
            Academic Level
        </label>

        <input
            id="level"
            name="level"
            value="{{ old(
                'level',
                $class?->level
            ) }}"
            placeholder="e.g. Form 3"
        >

    </div>


    <div class="form-group">

        <label for="academic_year">
            Academic Year
        </label>

        <input
            id="academic_year"
            name="academic_year"
            value="{{ old(
                'academic_year',
                $class?->academic_year ?? date('Y')
            ) }}"
            placeholder="{{ date('Y') }}"
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

            <option
                value="active"
                @selected(
                    old(
                        'status',
                        $class?->status ?? 'active'
                    ) === 'active'
                )
            >
                Active
            </option>

            <option
                value="inactive"
                @selected(
                    old(
                        'status',
                        $class?->status
                    ) === 'inactive'
                )
            >
                Inactive
            </option>

        </select>

    </div>

</div>