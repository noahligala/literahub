@props([
    'student' => null,
    'school',
    'classes' => collect(),
])

@php
    $editing = !is_null($student);

    $studentClass = $editing
        ? $student
            ->studentClasses
            ->first()
        : null;

    $currentClass =
        $studentClass?->id;

    $currentStream =
        $studentClass?->pivot?->stream_id;

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


    {{-- =====================================================
         Student Name
         ===================================================== --}}

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
            autocomplete="name"
            required
        >

        @error('name')
            <div class="field-error">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- =====================================================
         Admission Number
         ===================================================== --}}

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


    {{-- =====================================================
         Email
         ===================================================== --}}

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


    {{-- =====================================================
         Phone
         ===================================================== --}}

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
            autocomplete="tel"
        >

        @error('phone')
            <div class="field-error">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- =====================================================
         Class
         ===================================================== --}}

    <div class="form-group">

        <label for="school_class_id">
            Class
        </label>

        <select
            id="school_class_id"
            name="school_class_id"
            data-student-class-select
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

                    @if($class->academic_year)
                        — {{ $class->academic_year }}
                    @endif
                </option>

            @endforeach

        </select>

        <div class="field-help">
            A student may belong to a class without
            being assigned to a stream.
        </div>

        @error('school_class_id')
            <div class="field-error">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- =====================================================
         Stream
         ===================================================== --}}

    <div class="form-group">

        <label for="stream_id">
            Stream
        </label>

        <select
            id="stream_id"
            name="stream_id"
            data-student-stream-select
        >

            <option
                value=""
                data-class-id=""
            >
                No Stream Assigned
            </option>

            @foreach($classes as $class)

                @foreach($class->streams as $stream)

                    <option
                        value="{{ $stream->id }}"
                        data-class-id="{{ $class->id }}"
                        @selected(
                            old(
                                'stream_id',
                                $currentStream
                            ) == $stream->id
                        )
                    >
                        {{ $class->name }}
                        — {{ $stream->name }}
                    </option>

                @endforeach

            @endforeach

        </select>

        <div class="field-help">
            Optional. Streams are filtered according
            to the selected class.
        </div>

        @error('stream_id')
            <div class="field-error">
                {{ $message }}
            </div>
        @enderror

    </div>


    {{-- =====================================================
         Status
         ===================================================== --}}

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


    {{-- =====================================================
         Password
         ===================================================== --}}

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


    {{-- =====================================================
         Password Confirmation
         ===================================================== --}}

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


{{-- =========================================================
     Class / Stream filtering
     ========================================================= --}}

<script>
    document.addEventListener('DOMContentLoaded', () => {

        const classSelect = document.querySelector(
            '[data-student-class-select]'
        );

        const streamSelect = document.querySelector(
            '[data-student-stream-select]'
        );

        if (!classSelect || !streamSelect) {
            return;
        }

        const filterStreams = () => {

            const selectedClass =
                classSelect.value;

            const currentStream =
                streamSelect.value;

            let selectedStillAvailable = false;

            Array.from(
                streamSelect.options
            ).forEach((option) => {

                /*
                 * Always display the
                 * "No Stream Assigned" option.
                 */
                if (!option.value) {
                    option.hidden = false;
                    option.disabled = false;

                    return;
                }

                const streamClass =
                    option.dataset.classId;

                const visible =
                    selectedClass !== ''
                    && streamClass === selectedClass;

                option.hidden = !visible;
                option.disabled = !visible;

                if (
                    visible
                    && option.value === currentStream
                ) {
                    selectedStillAvailable = true;
                }

            });

            /*
             * Remove an old stream selection when
             * the user changes to another class.
             */
            if (!selectedStillAvailable) {
                streamSelect.value = '';
            }

            /*
             * A stream cannot be selected when
             * no class is selected.
             */
            streamSelect.disabled =
                selectedClass === '';
        };


        classSelect.addEventListener(
            'change',
            filterStreams
        );

        filterStreams();

    });
</script>