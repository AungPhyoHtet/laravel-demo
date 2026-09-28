@props([
    'name',
    'label' => null,
    'hint' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
])

@php
    $id ??= $name;
    $errorKey = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
    $currentValue = $type === 'password' ? null : old($errorKey, $value);
@endphp

<fieldset class="fieldset">
    @if ($label)
        <legend class="fieldset-legend">{{ $label }}</legend>
    @endif

    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
        @if (filled($currentValue)) value="{{ $currentValue }}" @endif
        {{ $attributes->class(['input w-full', 'input-error' => $errors->has($errorKey)]) }}>

    @if ($hint && ! $errors->has($errorKey))
        <p class="label">{{ $hint }}</p>
    @endif

    <x-input-error :messages="$errors->get($errorKey)" />
</fieldset>
