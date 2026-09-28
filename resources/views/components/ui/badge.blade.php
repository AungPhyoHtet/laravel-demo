@props([
    'variant' => null,
    'size' => null,
    'outline' => false,
])

<span
    {{ $attributes->class([
        'badge',
        match ($variant) {
            'primary' => 'badge-primary',
            'secondary' => 'badge-secondary',
            'accent' => 'badge-accent',
            'neutral' => 'badge-neutral',
            'info' => 'badge-info',
            'success' => 'badge-success',
            'warning' => 'badge-warning',
            'error' => 'badge-error',
            'ghost' => 'badge-ghost',
            default => null,
        },
        match ($size) {
            'xs' => 'badge-xs',
            'sm' => 'badge-sm',
            'lg' => 'badge-lg',
            'xl' => 'badge-xl',
            default => null,
        },
        'badge-outline' => $outline,
    ]) }}>{{ $slot }}</span>
