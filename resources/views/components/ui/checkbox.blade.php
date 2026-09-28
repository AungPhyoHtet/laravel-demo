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
    $isChecked = session()->hasOldInput() ? in_array((string) $value, array_map('strval', (array) old($errorKey)), true) : $checked;
@endphp

<label class="label">
    <input id="{{ $id }}" name="{{ $name }}" type="checkbox" value="{{ $value }}" @checked($isChecked)
        {{ $attributes->class([
            'checkbox',
            match ($variant) {
                'primary' => 'checkbox-primary',
                'secondary' => 'checkbox-secondary',
                'accent' => 'checkbox-accent',
                'neutral' => 'checkbox-neutral',
                'info' => 'checkbox-info',
                'success' => 'checkbox-success',
                'warning' => 'checkbox-warning',
                'error' => 'checkbox-error',
                default => null,
            },
            match ($size) {
                'xs' => 'checkbox-xs',
                'sm' => 'checkbox-sm',
                'lg' => 'checkbox-lg',
                'xl' => 'checkbox-xl',
                default => null,
            },
        ]) }}>
    {{ $label ?? $slot }}
</label>
