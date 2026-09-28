@props([
    'name',
    'value',
    'label' => null,
    'id' => null,
    'checked' => false,
    'variant' => null,
    'size' => null,
])

@php
    $id ??= $name.'-'.$value;
    $errorKey = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
    $isChecked = session()->hasOldInput() ? (string) old($errorKey) === (string) $value : $checked;
@endphp

<label class="label">
    <input id="{{ $id }}" name="{{ $name }}" type="radio" value="{{ $value }}" @checked($isChecked)
        {{ $attributes->class([
            'radio',
            match ($variant) {
                'primary' => 'radio-primary',
                'secondary' => 'radio-secondary',
                'accent' => 'radio-accent',
                'neutral' => 'radio-neutral',
                'info' => 'radio-info',
                'success' => 'radio-success',
                'warning' => 'radio-warning',
                'error' => 'radio-error',
                default => null,
            },
            match ($size) {
                'xs' => 'radio-xs',
                'sm' => 'radio-sm',
                'lg' => 'radio-lg',
                'xl' => 'radio-xl',
                default => null,
            },
        ]) }}>
    {{ $label ?? $slot }}
</label>
