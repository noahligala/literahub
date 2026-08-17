<x-layouts.dashboard title="Edit Student — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Students
            </span>

            <h1>
                Edit {{ $student->name }}
            </h1>

            <p>
                Update student information, class and stream
                placement, admission details and account status.
            </p>

        </div>


        <div class="actions">

            <a
                href="{{ route(
                    'school.students.show',
                    $student
                ) }}"
                class="button button-secondary"
            >
                View Student
            </a>

        </div>

    </div>


    <form
        method="POST"
        action="{{ route(
            'school.students.update',
            $student
        ) }}"
        class="card form-card"
    >

        @csrf
        @method('PUT')


        <div class="form-card__header">

            <div>

                <span class="eyebrow">
                    Student Account
                </span>

                <h3>
                    Student Information
                </h3>

                <p>
                    Edit personal information, admission
                    details, class placement, stream assignment
                    and access settings.
                </p>

            </div>

        </div>


        {{-- =====================================================
             Student Form Component
             ===================================================== --}}

        <x-forms.student
            :student="$student"
            :school="$school"
            :classes="$classes"
        />


        {{-- =====================================================
             Form Actions
             ===================================================== --}}

        <div class="form-actions">

            <button
                type="submit"
                class="button"
            >
                Save Changes
            </button>


            <a
                href="{{ route(
                    'school.students.show',
                    $student
                ) }}"
                class="button button-secondary"
            >
                Cancel
            </a>

        </div>

    </form>

</x-layouts.dashboard>