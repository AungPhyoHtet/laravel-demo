@props([
    'variant' => null,
    'size' => null,
    'outline' => false,
    'href' => null,
    'type' => 'submit',
])

@php
    $classes = [
        'btn',
        match ($variant) {
            'primary' => 'btn-primary',
            'secondary' => 'btn-secondary',
            'accent' => 'btn-accent',
            'neutral' => 'btn-neutral',
            'info' => 'btn-info',
            'success' => 'btn-success',
            'warning' => 'btn-warning',
            'error' => 'btn-error',
            'ghost' => 'btn-ghost',
            'link' => 'btn-link',
            default => null,
        },
        match ($size) {
            'xs' => 'btn-xs',
            'sm' => 'btn-sm',
            'lg' => 'btn-lg',
            'xl' => 'btn-xl',
            default => null,
        },
        'btn-outline' => $outline,
    ];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
