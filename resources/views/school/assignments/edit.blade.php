<x-layouts.dashboard title="Edit Assignment — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Assignment
            </span>

            <h1>
                Edit {{ $assignment->title }}
            </h1>

            <p>
                Update the assignment, reading range,
                class or publication status.
            </p>

        </div>

    </div>


    @if ($errors->any())

        <div class="alert alert--error">

            <strong>
                Please correct the following:
            </strong>

            <ul>
                @foreach ($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach
            </ul>

        </div>

    @endif


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
            :books="$books ?? collect()"
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