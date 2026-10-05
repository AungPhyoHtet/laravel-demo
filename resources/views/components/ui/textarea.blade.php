@props([
    'name',
    'label' => null,
    'hint' => null,
    'id' => null,
    'value' => null,
])

@php
    $id ??= $name;
    $errorKey = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
@endphp

<fieldset class="fieldset">
    @if ($label)
        <legend class="fieldset-legend">{{ $label }}</legend>
    @endif

    <textarea id="{{ $id }}" name="{{ $name }}"
        {{ $attributes->class(['textarea w-full', 'textarea-error' => $errors->has($errorKey)]) }}>{{ old($errorKey, $value) }}</textarea>

    @if ($hint && ! $errors->has($errorKey))
        <p class="label">{{ $hint }}</p>
    @endif

    <x-input-error :messages="$errors->get($errorKey)" />
</fieldset>
