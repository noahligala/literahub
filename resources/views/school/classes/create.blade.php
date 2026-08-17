<x-layouts.dashboard title="Create Class — LiteraHub">

    <h1>Create Class</h1>

    <form
        method="POST"
        action="{{ route(
            'school.classes.store'
        ) }}"
        class="card"
    >
        @csrf

        @include(
            'school.classes._form'
        )

        <div class="actions">
            <button class="button">
                Create Class
            </button>

            <a
                href="{{ route(
                    'school.classes.index'
                ) }}"
                class="button button-secondary"
            >
                Cancel
            </a>
        </div>

    </form>