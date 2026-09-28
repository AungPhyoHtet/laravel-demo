@props([
    'name',
    'options' => [],
    'label' => null,
    'id' => null,
    'value' => null,
    'placeholder' => null,
])

@php
    $id ??= $name;
    $errorKey = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
    $selectedValue = old($errorKey, $value instanceof BackedEnum ? $value->value : $value);
@endphp

<div class="flex flex-col gap-1.5">
    @if ($label)
        <x-ui.label :for="$id">{{ $label }}</x-ui.label>
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

    <x-input-error :messages="$errors->get($errorKey)" />
</div>
