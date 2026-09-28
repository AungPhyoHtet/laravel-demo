@props([
    'legend' => null,
])

<fieldset {{ $attributes->class(['fieldset bg-base-200 border-base-300 rounded-box border p-4']) }}>
    @if ($legend)
        <legend class="fieldset-legend">{{ $legend }}</legend>
    @endif

    {{ $slot }}
</fieldset>
