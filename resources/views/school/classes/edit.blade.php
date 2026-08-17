<x-layouts.dashboard title="Edit Class — LiteraHub">

    <h1>
        Edit {{ $class->name }}
    </h1>

    <form
        method="POST"
        action="{{ route(
            'school.classes.update',
            $class
        ) }}"
        class="card"
    >
        @csrf
        @method('PUT')

        @include(
            'school.classes._form'
        )

        <div class="actions">

            <button class="button">
                Save Changes
            </button>

            <a
                href="{{ route(
                    'school.classes.show',
                    $class
                ) }}"
                class="button button-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</x-layouts.dashboard>