<x-layouts.dashboard title="Edit Assignment — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Assignment
            </span>

            <h1>
                Edit {{ $assignment->title }}
            </h1>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route(
            'school.assignments.update',
            $assignment
        ) }}"
        class="card form-card"
    >

        @csrf
        @method('PUT')


        <x-forms.assignment
            :assignment="$assignment"
            :classes="$classes"
            :resources="$resources ?? collect()"
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
                    'school.assignments.show',
                    $assignment
                ) }}"
                class="button button-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</x-layouts.dashboard>