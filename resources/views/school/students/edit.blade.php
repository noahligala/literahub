<x-layouts.dashboard title="Edit Student — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Students
            </span>

            <h1>
                Edit {{ $student->name }}
            </h1>

            <p>
                Update student information, class assignment
                and account status.
            </p>

        </div>

        <div class="actions">

            <a
                href="{{ route(
                    'school.students.show',
                    $student
                ) }}"
                class="button button-secondary"
            >
                View Student
            </a>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route(
            'school.students.update',
            $student
        ) }}"
        class="card form-card"
    >

        @csrf
        @method('PUT')


        <div class="form-card__header">

            <h3>
                Student Information
            </h3>

            <p>
                Edit personal information, admission details
                and access settings.
            </p>

        </div>


        @include(
            'school.students._form'
        )


        <div class="form-actions">

            <button
                type="submit"
                class="button"
            >
                Save Changes
            </button>


            <a
                href="{{ route(
                    'school.students.show',
                    $student
                ) }}"
                class="button button-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</x-layouts.dashboard>