<x-layouts.dashboard title="Add Stream — LiteraHub">

    <span class="eyebrow">
        {{ $class->name }}
    </span>

    <h1>Add Stream</h1>

    <form
        method="POST"
        action="{{ route(
            'school.streams.store',
            $class
        ) }}"
        class="card"
    >
        @csrf

        @include(
            'school.classes.streams._form'
        )

        <div class="actions">

            <button class="button">
                Create Stream
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