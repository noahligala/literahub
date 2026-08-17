@props([
    'status',
])

@php
    $class = match ($status) {
        'published',
        'approved' =>
            'badge--success',

        'under_review',
        'changes_requested' =>
            'badge--warning',

        'rejected',
        'archived' =>
            'badge--danger',

        'draft' =>
            'badge--muted',

        default =>
            'badge--muted',
    };

    $label = str($status)
        ->replace('_', ' ')
        ->title();
@endphp

<span class="badge {{ $class }}">
    {{ $label }}
</span>