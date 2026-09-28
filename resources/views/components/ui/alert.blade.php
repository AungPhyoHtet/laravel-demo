@props([
    'variant' => null,
])

<div role="alert"
    {{ $attributes->class([
        'alert',
        match ($variant) {
            'info' => 'alert-info',
            'success' => 'alert-success',
            'warning' => 'alert-warning',
            'error' => 'alert-error',
            default => null,
        },
    ]) }}>
    {{ $slot }}
</div>
