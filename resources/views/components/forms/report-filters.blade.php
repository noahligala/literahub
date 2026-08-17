@props([
    'classes' => collect(),
    'teachers' => collect(),
    'showTeacher' => true,
])

<form
    method="GET"
    class="card report-filter-card"
>

    <div class="form-grid">


        <div class="form-group">

            <label for="from">
                From
            </label>

            <input
                id="from"
                type="date"
                name="from"
                value="{{ request('from') }}"
            >

        </div>


        <div class="form-group">

            <label for="to">
                To
            </label>

            <input
                id="to"
                type="date"
                name="to"
                value="{{ request('to') }}"
            >

        </div>


        <div class="form-group">

            <label for="class_id">
                Class
            </label>

            <select
                id="class_id"
                name="class_id"
            >

                <option value="">
                    All Classes
                </option>

                @foreach($classes as $class)

                    <option
                        value="{{ $class->id }}"
                        @selected(
                            request('class_id')
                                == $class->id
                        )
                    >
                        {{ $class->name }}
                    </option>

                @endforeach

            </select>

        </div>


        @if($showTeacher)

            <div class="form-group">

                <label for="teacher_id">
                    Teacher
                </label>

                <select
                    id="teacher_id"
                    name="teacher_id"
                >

                    <option value="">
                        All Teachers
                    </option>

                    @foreach($teachers as $teacher)

                        <option
                            value="{{ $teacher->id }}"
                            @selected(
                                request('teacher_id')
                                    == $teacher->id
                            )
                        >
                            {{ $teacher->name }}
                        </option>

                    @endforeach

                </select>

            </div>

        @endif

    </div>


    <div class="form-actions">

        <button
            type="submit"
            class="button"
        >
            Apply Filters
        </button>

        <a
            href="{{ url()->current() }}"
            class="button button-secondary"
        >
            Reset
        </a>

    </div>

</form>