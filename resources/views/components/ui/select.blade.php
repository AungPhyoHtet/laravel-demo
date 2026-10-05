@props([
    'name',
    'options' => [],
    'label' => null,
    'hint' => null,
    'id' => null,
    'value' => null,
    'placeholder' => null,
])

@php
    $id ??= $name;
    $errorKey = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
    $selectedValue = old($errorKey, $value instanceof BackedEnum ? $value->value : $value);
@endphp

<fieldset class="fieldset">
    @if ($label)
        <legend class="fieldset-legend">{{ $label }}</legend>
    @endif

    <select id="{{ $id }}" name="{{ $name }}"
        {{ $attributes->class(['select w-full', 'select-error' => $errors->has($errorKey)]) }}>
        @if ($placeholder)
            <option value="" disabled @selected(blank($selectedValue))>{{ $placeholder }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) $optionValue === (string) $selectedValue)>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @if ($hint && ! $errors->has($errorKey))
        <p class="label">{{ $hint }}</p>
    @endif

    <x-input-error :messages="$errors->get($errorKey)" />
</fieldset>
