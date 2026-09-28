@props([
    'action',
    'title' => null,
    'description' => null,
    'method' => 'POST',
])

@php
    $method = strtoupper($method);
@endphp

<div class="m-auto w-full max-w-sm">
    @if ($title || $description)
        <div class="mb-4 text-center">
            @if ($title)
                <h1 class="text-2xl font-bold">{{ $title }}</h1>
            @endif

            @if ($description)
                <p class="mt-1 text-base-content/70">{{ $description }}</p>
            @endif
        </div>
    @endif

    <form action="{{ $action }}" method="{{ $method === 'GET' ? 'GET' : 'POST' }}" novalidate
        {{ $attributes->class(['flex flex-col gap-2']) }}>
        @unless ($method === 'GET')
            @csrf
        @endunless

        @unless (in_array($method, ['GET', 'POST'], true))
            @method($method)
        @endunless

        {{ $slot }}
    </form>
</div>
