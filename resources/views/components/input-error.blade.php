@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-error text-sm']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
