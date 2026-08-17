@props([
    'student' => null,
    'school' => null,
    'classes' => collect(),
])

@php
    $editing = !is_null($student);

    $membership = $editing && $school
        ? $student
            ->schools()
            ->where('schools.id', $school->id)
            ->first()?->pivot
        : null;

    $currentClass = $editing
        ? $student
            ->studentClasses
            ->first()?->id
        : null;
@endphp

<div class="form-grid">

    <div class="form-group">

        <label for="name">
            Student Name
        </label>

        <input
            id="name"
            name="name"
            value="{{ old(
                'name',
                $student?->name
            ) }}"
            required
        >

        @error('name')
            <div class="field-error">
                {{ $message }}
            </div>
        @enderror

    </div>


    <div class="form-group">

        <label for="admission_number">
            Admission Number
        </label>

        <input
            id="admission_number"
            name="admission_number"
            value="{{ old(
                'admission_number',
                $membership?->reference_number
            ) }}"
            required
        >

        @error('admission_number')
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
            type="email"
            name="email"
            value="{{ old(
                'email',
                $student?->email
            ) }}"
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


    <div class="form-group">

        <label for="status">
            Account Status
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
                        $student?->status ?? 'active'
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
                        $student?->status
                    ) === 'inactive'
                )
            >
                Inactive
            </option>

            <option
                value="suspended"
                @selected(
                    old(
                        'status',
                        $student?->status
                    ) === 'suspended'
                )
            >
                Suspended
            </option>

        </select>

        @error('status')
            <div class="field-error">
                {{ $message }}
            </div>
        @enderror

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
            type="password"
            name="password"
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
            type="password"
            name="password_confirmation"
            autocomplete="new-password"
            @required(!$editing)
        >

    </div>

</div>