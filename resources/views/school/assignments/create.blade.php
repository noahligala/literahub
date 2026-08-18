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
                Assign licensed literature and academic work
                to one of your classes.
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
            'school.assignments.store'
        ) }}"
        class="card form-card"
    >

        @csrf


        <x-forms.assignment
            :classes="$classes"
            :books="$books ?? collect()"
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