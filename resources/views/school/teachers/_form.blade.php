@php
    $editing = isset($teacher);

    $membership =
        $editing
            ? $teacher
                ->schools()
                ->where(
                    'schools.id',
                    $school->id
                )
                ->first()?->pivot
            : null;

    $selectedClasses =
        old(
            'class_ids',
            $editing
                ? $teacher
                    ->teacherClasses
                    ->pluck('id')
                    ->all()
                : []
        );
@endphp

<div class="form-grid">

    <div class="form-group">
        <label>Name</label>

        <input
            name="name"
            value="{{ old(
                'name',
                $teacher->name ?? ''
            ) }}"
            required
        >
    </div>

    <div class="form-group">
        <label>Employee Number</label>

        <input
            name="employee_number"
            value="{{ old(
                'employee_number',
                $membership?->reference_number
            ) }}"
        >
    </div>

    <div class="form-group">
        <label>Email</label>

        <input
            type="email"
            name="email"
            value="{{ old(
                'email',
                $teacher->email ?? ''
            ) }}"
            required
        >
    </div>

    <div class="form-group">
        <label>Phone</label>

        <input
            name="phone"
            value="{{ old(
                'phone',
                $teacher->phone ?? ''
            ) }}"
        >
    </div>

    <div class="form-group">
        <label>Status</label>

        <select name="status">

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
                            $teacher->status
                                ?? 'active'
                        ) === $value
                    )
                >
                    {{ $label }}
                </option>

            @endforeach

        </select>
    </div>

    <div class="form-group">
        <label>Password</label>

        <input
            type="password"
            name="password"
        >
    </div>

    <div class="form-group">
        <label>Confirm Password</label>

        <input
            type="password"
            name="password_confirmation"
        >
    </div>

</div>

<div class="form-group">

    <label>
        Assigned Classes
    </label>

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

            <p>
                No classes have been created yet.
            </p>

        @endforelse

    </div>

</div>