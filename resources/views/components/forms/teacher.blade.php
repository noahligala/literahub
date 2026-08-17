@props([
    'teacher' => null,
    'school',
    'classes' => collect(),
])

@php
    $editing = !is_null($teacher);

    $membership = $editing
        ? $teacher
            ->schools()
            ->where('schools.id', $school->id)
            ->first()?->pivot
        : null;

    $selectedClasses = old(
        'class_ids',
        $editing
            ? $teacher
                ->teachingClasses
                ->pluck('id')
                ->all()
            : []
    );
@endphp


<div class="form-grid">

    <div class="form-group">

        <label for="name">
            Teacher Name
        </label>

        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name', $teacher?->name) }}"
            placeholder="Enter full name"
            required
        >

        @error('name')
            <div class="field-error">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="form-group">

        <label for="employee_number">
            Employee Number
        </label>

        <input
            id="employee_number"
            name="employee_number"
            type="text"
            value="{{ old(
                'employee_number',
                $membership?->reference_number
            ) }}"
            placeholder="e.g. TSC-001"
        >

        @error('employee_number')
            <div class="field-error">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="form-group">

        <label for="email">
            Email Address
        </label>

        <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email', $teacher?->email) }}"
            placeholder="teacher@example.com"
            required
        >

        @error('email')
            <div class="field-error">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="form-group">

        <label for="phone">
            Phone Number
        </label>

        <input
            id="phone"
            name="phone"
            type="tel"
            value="{{ old('phone', $teacher?->phone) }}"
            placeholder="+254..."
        >

        @error('phone')
            <div class="field-error">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="form-group">

        <label for="status">
            Account Status
        </label>

        <select
            id="status"
            name="status"
            required
        >

            @foreach([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'suspended' => 'Suspended',
            ] as $value => $label)

                <option
                    value="{{ $value }}"
                    @selected(
                        old(
                            'status',
                            $teacher?->status ?? 'active'
                        ) === $value
                    )
                >
                    {{ $label }}
                </option>

            @endforeach

        </select>

    </div>


    <div class="form-group">

        <label for="password">
            {{ $editing
                ? 'New Password'
                : 'Password'
            }}
        </label>

        <input
            id="password"
            name="password"
            type="password"
            autocomplete="new-password"
            @required(!$editing)
        >

        @if($editing)
            <div class="field-help">
                Leave blank to keep the existing password.
            </div>
        @endif

        @error('password')
            <div class="field-error">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="form-group">

        <label for="password_confirmation">
            Confirm Password
        </label>

        <input
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            autocomplete="new-password"
            @required(!$editing)
        >

    </div>

</div>


<div class="form-section">

    <div class="form-section__heading">

        <h3>
            Assigned Classes
        </h3>

        <p>
            Select the classes this teacher is responsible for.
        </p>

    </div>


    <div class="checkbox-grid">

        @forelse($classes as $class)

            <label class="checkbox-card">

                <input
                    type="checkbox"
                    name="class_ids[]"
                    value="{{ $class->id }}"
                    @checked(
                        in_array(
                            $class->id,
                            $selectedClasses
                        )
                    )
                >

                <span>
                    {{ $class->name }}
                </span>

            </label>

        @empty

            <div class="empty-state">
                <p>
                    No classes have been created yet.
                </p>
            </div>

        @endforelse

    </div>

</div>