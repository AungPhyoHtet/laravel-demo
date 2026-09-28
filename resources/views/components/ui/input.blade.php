@props([
    'name',
    'label' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
])

@php
    $id ??= $name;
    $errorKey = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
    $currentValue = $type === 'password' ? null : old($errorKey, $value);
@endphp

<div class="flex flex-col gap-1.5">
    @if ($label)
        <x-ui.label :for="$id">{{ $label }}</x-ui.label>
    @endif

    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
        @if (filled($currentValue)) value="{{ $currentValue }}" @endif
        {{ $attributes->class(['input w-full', 'input-error' => $errors->has($errorKey)]) }}>

    <x-input-error :messages="$errors->get($errorKey)" />
</div>
