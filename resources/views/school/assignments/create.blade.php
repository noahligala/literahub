<x-layouts.dashboard title="Create Assignment — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Academic Activity
            </span>

            <h1>
                Create Assignment
            </h1>

            <p>
                Assign literature and academic work
                to a class.
            </p>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route(
            'school.assignments.store'
        ) }}"
        class="card form-card"
    >

        @csrf


        <x-forms.assignment
            :classes="$classes"
            :resources="$resources ?? collect()"
        />


        <div class="form-actions">

            <button
                type="submit"
                class="button"
            >
                Create Assignment
            </button>

            <a
                href="{{ route(
                    'school.assignments.index'
                ) }}"
                class="button button-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</x-layouts.dashboard>