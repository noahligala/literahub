<x-layouts.dashboard title="Edit Teacher — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Teachers
            </span>

            <h1>
                Edit {{ $teacher->name }}
            </h1>

            <p>
                Update account details and teaching assignments.
            </p>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route(
            'school.teachers.update',
            $teacher
        ) }}"
        class="card form-card"
    >

        @csrf
        @method('PUT')


        <x-forms.teacher
            :teacher="$teacher"
            :school="$school"
            :classes="$classes"
        />


        <div class="form-actions">

            <button
                type="submit"
                class="button"
            >
                Save Changes
            </button>

            <a
                href="{{ route(
                    'school.teachers.show',
                    $teacher
                ) }}"
                class="button button-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</x-layouts.dashboard>