<x-layouts.dashboard title="Add Student — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Students
            </span>

            <h1>
                Add Student
            </h1>

            <p>
                Create a learner account for
                {{ $school->name }}.
            </p>

        </div>


        <div class="actions">

            <a
                href="{{ route('school.students.index') }}"
                class="button button-secondary"
            >
                Back to Students
            </a>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route('school.students.store') }}"
        class="card form-card"
    >

        @csrf


        <div class="form-card__header">

            <div>

                <span class="eyebrow">
                    Student Information
                </span>

                <h3>
                    Account Details
                </h3>

                <p>
                    Enter the learner's personal,
                    admission and access information.
                </p>

            </div>

        </div>


        <x-forms.student
            :school="$school"
            :classes="$classes"
        />


        <div class="form-actions">

            <button
                type="submit"
                class="button"
            >
                Create Student
            </button>


            <a
                href="{{ route('school.students.index') }}"
                class="button button-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</x-layouts.dashboard>