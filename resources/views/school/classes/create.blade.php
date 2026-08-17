<x-layouts.dashboard title="Create Class — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Academic Structure
            </span>

            <h1>
                Create Class
            </h1>

            <p>
                Add a new class or year group to
                {{ $school->name }}.
            </p>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route('school.classes.store') }}"
        class="card form-card"
    >

        @csrf


        <div class="form-card__header">

            <h3>
                Class Information
            </h3>

            <p>
                Define the class name, level and academic year.
            </p>

        </div>


        <x-forms.school-class />


        <div class="form-actions">

            <button
                type="submit"
                class="button"
            >
                Create Class
            </button>

            <a
                href="{{ route('school.classes.index') }}"
                class="button button-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</x-layouts.dashboard>