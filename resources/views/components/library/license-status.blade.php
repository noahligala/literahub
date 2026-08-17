@props([
    'status',
])

@php
    $class = match ($status) {
        'active' =>
            'badge--success',

        'pending' =>
            'badge--warning',

        'expired',
        'revoked',
        'suspended' =>
            'badge--danger',

        default =>
            'badge--muted',
    };
@endphp

<span class="badge {{ $class }}">
    {{ str($status)->replace('_', ' ')->title() }}
</span>