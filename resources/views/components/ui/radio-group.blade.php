@props([
    'name',
    'options' => [],
    'label' => null,
    'hint' => null,
    'value' => null,
    'variant' => null,
    'size' => null,
])

@php
    $errorKey = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
    $selectedValue = $value instanceof BackedEnum ? $value->value : $value;
@endphp

<fieldset {{ $attributes->class(['fieldset']) }}>
    @if ($label)
        <legend class="fieldset-legend">{{ $label }}</legend>
    @endif

    @foreach ($options as $optionValue => $optionLabel)
        <x-ui.radio :name="$name" :value="$optionValue" :label="$optionLabel" :variant="$variant" :size="$size"
            :checked="(string) $optionValue === (string) $selectedValue" />
    @endforeach

    @if ($hint && ! $errors->has($errorKey))
        <p class="label">{{ $hint }}</p>
    @endif

    <x-input-error :messages="$errors->get($errorKey)" />
</fieldset>
