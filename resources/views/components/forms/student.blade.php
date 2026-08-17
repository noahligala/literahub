@props([
    'student' => null,
    'school',
    'classes' => collect(),
])

@php
    $editing = !is_null($student);

    $currentClass =
        $editing
            ? $student
                ->studentClasses
                ->first()?->id
            : null;

    $membership =
        $editing
            ? $student
                ->schools()
                ->where(
                    'schools.id',
                    $school->id
                )
                ->first()?->pivot
            : null;
@endphp


<div class="form-grid">


    {{-- Student Name --}}
    <div class="form-group">

        <label for="name">
            Student Name
        </label>

        <input
            id="name"
            name="name"
            type="text"
            value="{{ old(
                'name',
                $student?->name
            ) }}"
            placeholder="Enter full name"
            required
        >

        @error('name')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    {{-- Admission Number --}}
    <div class="form-group">

        <label for="admission_number">
            Admission Number
        </label>

        <input
            id="admission_number"
            name="admission_number"
            type="text"
            value="{{ old(
                'admission_number',
                $membership?->reference_number
            ) }}"
            placeholder="e.g. ADM-2026-001"
            required
        >

        @error('admission_number')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    {{-- Email --}}
    <div class="form-group">

        <label for="email">
            Email Address
        </label>

        <input
            id="email"
            name="email"
            type="email"
            value="{{ old(
                'email',
                $student?->email
            ) }}"
            placeholder="student@example.com"
            autocomplete="email"
            required
        >

        @error('email')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    {{-- Phone --}}
    <div class="form-group">

        <label for="phone">
            Phone Number
        </label>

        <input
            id="phone"
            name="phone"
            type="tel"
            value="{{ old(
                'phone',
                $student?->phone
            ) }}"
            placeholder="+254..."
        >

        @error('phone')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    {{-- Class --}}
    <div class="form-group">

        <label for="school_class_id">
            Class
        </label>

        <select
            id="school_class_id"
            name="school_class_id"
        >

            <option value="">
                No Class Assigned
            </option>

            @foreach($classes as $class)

                <option
                    value="{{ $class->id }}"
                    @selected(
                        old(
                            'school_class_id',
                            $currentClass
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


    {{-- Status --}}
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
                            $student?->status ?? 'active'
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


    {{-- Password --}}
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
                Leave blank to keep the current password.
            </div>

        @endif

        @error('password')

            <div class="field-error">
                {{ $message }}
            </div>

        @enderror

    </div>


    {{-- Confirm Password --}}
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