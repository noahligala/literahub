<div class="card assignment-details">

    @if ($assignment->book)

        <div class="assignment-resource">

            <span class="field-label">
                Assigned Book
            </span>

            <h2>
                {{ $assignment->book->title }}
            </h2>

            <p>
                {{ $assignment
                    ->book
                    ->authors
                    ->pluck('name')
                    ->join(', ')
                    ?: 'Unknown author'
                }}
            </p>


            <a
                href="{{ route(
                    'school.library.show',
                    $assignment->book
                ) }}"
                class="button button-secondary button-small"
            >
                View Book
            </a>

        </div>

    @endif


    <div>

        <span class="field-label">
            Instructions
        </span>

        <p>
            {{ $assignment->instructions
                ?: 'No additional instructions.'
            }}
        </p>

    </div>


    @if (
        $assignment->start_page
        ||
        $assignment->end_page
    )

        <div>

            <span class="field-label">
                Reading Range
            </span>

            <strong>

                @if (
                    $assignment->start_page
                    &&
                    $assignment->end_page
                )

                    Pages
                    {{ $assignment->start_page }}
                    –
                    {{ $assignment->end_page }}

                @elseif (
                    $assignment->start_page
                )

                    From page
                    {{ $assignment->start_page }}

                @else

                    Up to page
                    {{ $assignment->end_page }}

                @endif

            </strong>

        </div>

    @endif


    @if ($assignment->starts_at)

        <div>

            <span class="field-label">
                Available From
            </span>

            <strong>
                {{ $assignment
                    ->starts_at
                    ->format(
                        'd M Y, H:i'
                    )
                }}
            </strong>

        </div>

    @endif


    <div>

        <span class="field-label">
            Due
        </span>

        <strong>
            {{ $assignment->due_at
                ? $assignment
                    ->due_at
                    ->format(
                        'd M Y, H:i'
                    )
                : 'No due date'
            }}
        </strong>

    </div>


    @if ($assignment->total_marks)

        <div>

            <span class="field-label">
                Total Marks
            </span>

            <strong>
                {{ $assignment->total_marks }}
            </strong>

        </div>

    @endif

</div>