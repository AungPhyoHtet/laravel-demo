@props(['messages'])

@if ($messages)
    @foreach ((array) $messages as $message)
        <p {{ $attributes->class(['label text-error']) }}>{{ $message }}</p>
    @endforeach
@endif
