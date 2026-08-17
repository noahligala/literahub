<x-layouts.dashboard title="Edit Stream — LiteraHub">

    <span class="eyebrow">
        {{ $class->name }}
    </span>

    <h1>
        Edit {{ $stream->name }}
    </h1>

    <form
        method="POST"
        action="{{ route(
            'school.streams.update',
            $stream
        ) }}"
        class="card"
    >
        @csrf
        @method('PUT')

        @include(
            'school.classes.streams._form'
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