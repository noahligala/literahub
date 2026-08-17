<x-layouts.dashboard title="{{ $teacher->name }} — LiteraHub">

    @php
        $membership = $teacher
            ->schools()
            ->where(
                'schools.id',
                $school->id
            )
            ->first()?->pivot;
    @endphp

    <div class="dashboard-heading">

        <div>
            <span class="eyebrow">
                Teacher
            </span>

            <h1>
                {{ $teacher->name }}
            </h1>

            <p>
                {{ $membership?->reference_number
                    ?? 'Teaching Staff'
                }}
            </p>
        </div>

        <div class="actions">

            <a
                href="{{ route(
                    'school.teachers.edit',
                    $teacher
                ) }}"
                class="button"
            >
                Edit Teacher
            </a>

        </div>

    </div>

    <div class="metric-grid">

        <article>
            <strong>
                {{ $teacher
                    ->teachingClasses
                    ->count()
                }}
            </strong>

            <span>Classes</span>
        </article>

        <article>
            <strong>
                {{ ucfirst(
                    $membership?->status
                    ?? $teacher->status
                ) }}
            </strong>

            <span>Status</span>
        </article>

        <article>
            <strong>0</strong>
            <span>Assignments</span>
        </article>

    </div>

    <div style="height:12px;"></div>

    <div class="card">

        <h3>
            Assigned Classes
        </h3>

        @forelse(
            $teacher->teachingClasses
            as $class
        )

            <a
                href="{{ route(
                    'school.classes.show',
                    $class
                ) }}"
                class="badge badge-primary"
            >
                {{ $class->name }}
            </a>

        @empty

            <p>
                No classes assigned.
            </p>

        @endforelse

    </div>

</x-layouts.dashboard>