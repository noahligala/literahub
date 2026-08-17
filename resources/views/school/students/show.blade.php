<x-layouts.dashboard title="Edit Student — LiteraHub">

    <div class="dashboard-heading">

        <div>
            <span class="eyebrow">
                Students
            </span>

            <h1>
                Edit {{ $student->name }}
            </h1>
        </div>

    </div>

    <form
        method="POST"
        action="{{ route(
            'school.students.update',
            $student
        ) }}"
        class="card"
    >
        @csrf
        @method('PUT')

        @include(
            'school.students._form'
        )

        <div class="actions">

            <button class="button">
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