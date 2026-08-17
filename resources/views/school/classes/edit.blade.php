<x-layouts.dashboard title="Edit Class — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Academic Structure
            </span>

            <h1>
                Edit {{ $class->name }}
            </h1>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route(
            'school.classes.update',
            $class
        ) }}"
        class="card form-card"
    >

        @csrf
        @method('PUT')


        <x-forms.school-class
            :class="$class"
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