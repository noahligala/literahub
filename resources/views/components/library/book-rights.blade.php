@props([
    'book',
    'license' => null,
])

@php
    /*
     * A licence can only narrow rights.
     * It cannot grant something forbidden by the book owner.
     */

    $rights = [
        [
            'label' => 'Online Reading',
            'book' => (bool) $book->allow_online_reading,
            'license' => $license
                ? (bool) (
                    $license->allow_student_reading
                    || $license->allow_teacher_reading
                )
                : null,
        ],

        [
            'label' => 'Student Borrowing',
            'book' => (bool) $book->allow_student_borrowing,
            'license' => $license
                ? (bool) $license->allow_student_borrowing
                : null,
        ],

        [
            'label' => 'Teacher Assignment',
            'book' => (bool) $book->allow_teacher_assignment,
            'license' => $license
                ? (bool) $license->allow_teacher_assignment
                : null,
        ],

        [
            'label' => 'Printing',
            'book' => (bool) $book->allow_print,
            'license' => $license
                ? (bool) $license->allow_print
                : null,
        ],

        [
            'label' => 'Download',
            'book' => (bool) $book->allow_download,
            'license' => $license
                ? (bool) $license->allow_download
                : null,
        ],
    ];
@endphp


<div class="rights-display">

    @foreach ($rights as $right)

        @php
            $allowed = $license
                ? $right['book'] && $right['license']
                : $right['book'];
        @endphp

        <div
            class="
                right-display
                {{ $allowed
                    ? 'right-display--allowed'
                    : 'right-display--denied'
                }}
            "
        >

            <span class="right-display__icon">

                @if ($allowed)

                    <svg viewBox="0 0 24 24">
                        <path d="m5 12 4 4L19 6"/>
                    </svg>

                @else

                    <svg viewBox="0 0 24 24">
                        <path d="M6 6l12 12"/>
                        <path d="M18 6 6 18"/>
                    </svg>

                @endif

            </span>


            <div>

                <strong>
                    {{ $right['label'] }}
                </strong>

                <small>
                    {{ $allowed
                        ? 'Permitted'
                        : 'Not permitted'
                    }}
                </small>

            </div>

        </div>

    @endforeach

</div>


<style>
    .rights-display {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
    }

    .right-display {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px;
        border: 1px solid var(--color-border);
        border-radius: var(--radius-md);
    }

    .right-display__icon {
        width: 26px;
        height: 26px;
        flex: 0 0 26px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: var(--color-surface-soft);
    }

    .right-display__icon svg {
        width: 13px;
        height: 13px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .right-display--allowed .right-display__icon {
        color: var(--success);
    }

    .right-display--denied .right-display__icon {
        color: var(--danger);
    }

    .right-display div {
        display: flex;
        flex-direction: column;
    }

    .right-display strong {
        color: var(--color-text);
        font-size: .61rem;
    }

    .right-display small {
        margin-top: 1px;
        color: var(--color-text-muted);
        font-size: .52rem;
    }

    @media (max-width: 700px) {
        .rights-display {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .rights-display {
            grid-template-columns: 1fr;
        }
    }
</style>