<x-layouts.dashboard title="Create Assignment — LiteraHub">

    <div class="dashboard-heading">

        <div>
            <span class="eyebrow">
                Academic Activity
            </span>

            <h1>
                Create Assignment
            </h1>
        </div>

    </div>

    <form
        method="POST"
        action="{{ route(
            'school.assignments.store'
        ) }}"
        class="card"
    >
        @csrf

        @include(
            'school.assignments._form'
        )

        <div class="actions">

            <button class="button">
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