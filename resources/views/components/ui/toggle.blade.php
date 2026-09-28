@props([
    'name',
    'label' => null,
    'id' => null,
    'value' => '1',
    'checked' => false,
    'variant' => null,
    'size' => null,
])

@php
    $id ??= $name;
    $errorKey = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
    $isChecked = session()->hasOldInput() ? (string) old($errorKey) === (string) $value : $checked;
@endphp

<label class="label">
    <input id="{{ $id }}" name="{{ $name }}" type="checkbox" value="{{ $value }}" @checked($isChecked)
        {{ $attributes->class([
            'toggle',
            match ($variant) {
                'primary' => 'toggle-primary',
                'secondary' => 'toggle-secondary',
                'accent' => 'toggle-accent',
                'neutral' => 'toggle-neutral',
                'info' => 'toggle-info',
                'success' => 'toggle-success',
                'warning' => 'toggle-warning',
                'error' => 'toggle-error',
                default => null,
            },
            match ($size) {
                'xs' => 'toggle-xs',
                'sm' => 'toggle-sm',
                'lg' => 'toggle-lg',
                'xl' => 'toggle-xl',
                default => null,
            },
        ]) }}>
    {{ $label ?? $slot }}
</label>
