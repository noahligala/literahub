<x-layouts.dashboard title="Add Teacher — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Teachers
            </span>

            <h1>
                Add Teacher
            </h1>

            <p>
                Create a teaching account for
                {{ $school->name }}.
            </p>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route('school.teachers.store') }}"
        class="card form-card"
    >

        @csrf


        <div class="form-card__header">

            <h3>
                Teacher Information
            </h3>

            <p>
                Add account information and class assignments.
            </p>

        </div>


        <x-forms.teacher
            :school="$school"
            :classes="$classes"
        />


        <div class="form-actions">

            <button
                type="submit"
                class="button"
            >
                Create Teacher
            </button>

            <a
                href="{{ route('school.teachers.index') }}"
                class="button button-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</x-layouts.dashboard>