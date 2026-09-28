@props([
    'name',
    'label' => null,
    'id' => null,
    'value' => null,
])

@php
    $id ??= $name;
    $errorKey = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
@endphp

<div class="flex flex-col gap-1.5">
    @if ($label)
        <x-ui.label :for="$id">{{ $label }}</x-ui.label>
    @endif

    <textarea id="{{ $id }}" name="{{ $name }}"
        {{ $attributes->class(['textarea w-full', 'textarea-error' => $errors->has($errorKey)]) }}>{{ old($errorKey, $value) }}</textarea>

    <x-input-error :messages="$errors->get($errorKey)" />
</div>
