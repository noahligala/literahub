<x-layouts.dashboard title="Add Teacher — LiteraHub">

    <h1>Add Teacher</h1>

    <form
        method="POST"
        action="{{ route(
            'school.teachers.store'
        ) }}"
        class="card"
    >
        @csrf

        @include(
            'school.teachers._form'
        )

        <div class="actions">
            <button class="button">
                Create Teacher
            </button>

            <a
                href="{{ route(
                    'school.teachers.index'
                ) }}"
                class="button button-secondary"
            >
                Cancel
            </a>
        </div>

    </form>

</x-layouts.dashboard>