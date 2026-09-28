@props([
    'name',
    'options' => [],
    'label' => null,
    'value' => null,
    'variant' => null,
    'size' => null,
])

@php
    $errorKey = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
    $selectedValue = $value instanceof BackedEnum ? $value->value : $value;
@endphp

<div role="radiogroup" {{ $attributes->class(['flex flex-col gap-1.5']) }}>
    @if ($label)
        <span class="label">{{ $label }}</span>
    @endif

    @foreach ($options as $optionValue => $optionLabel)
        <x-ui.radio :name="$name" :value="$optionValue" :label="$optionLabel" :variant="$variant" :size="$size"
            :checked="(string) $optionValue === (string) $selectedValue" />
    @endforeach

    <x-input-error :messages="$errors->get($errorKey)" />
</div>
