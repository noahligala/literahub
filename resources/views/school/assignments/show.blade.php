<x-layouts.dashboard title="{{ $assignment->title }} — LiteraHub">

    <div class="dashboard-heading">

        <div>

            <span class="eyebrow">
                Assignment
            </span>

            <h1>
                {{ $assignment->title }}
            </h1>

            <p>
                {{ $assignment
                    ->schoolClass
                    ->name
                }}
            </p>

        </div>

        <div class="actions">

            <a
                href="{{ route(
                    'school.assignments.edit',
                    $assignment
                ) }}"
                class="button"
            >
                Edit Assignment
            </a>

        </div>

    </div>

    <div class="metric-grid">

        <article>
            <strong>
                {{ $assignment
                    ->students
                    ->count()
                }}
            </strong>

            <span>Assigned Students</span>
        </article>

        <article>
            <strong>
                {{ $assignment
                    ->students
                    ->where(
                        'pivot.status',
                        'submitted'
                    )
                    ->count()
                }}
            </strong>

            <span>Submitted</span>
        </article>

        <article>
            <strong>
                {{ ucfirst(
                    $assignment->status
                ) }}
            </strong>

            <span>Status</span>
        </article>

    </div>

    <div style="height:12px;"></div>

    <div class="card">

        <span class="field-label">
            Instructions
        </span>

        <p>
            {{ $assignment->instructions
                ?: 'No additional instructions.'
            }}
        </p>

        <span class="field-label">
            Due
        </span>

        <strong>
            {{ $assignment->due_at
                ? $assignment
                    ->due_at
                    ->format('d M Y, H:i')
                : 'No due date'
            }}
        </strong>

    </div>

</x-layouts.dashboard>