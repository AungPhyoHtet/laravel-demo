@props([
    'name',
    'title' => null,
    'show' => false,
])

@php
    $titleId = $name.'-modal-title';
@endphp

<dialog x-data="{ open: @js((bool) $show) }"
    x-init="$watch('open', (value) => value ? $el.showModal() : $el.close()); if (open) $el.showModal();"
    x-on:open-modal.window="if ($event.detail === @js($name)) open = true"
    x-on:close-modal.window="if ($event.detail === @js($name)) open = false"
    x-on:close="open = false"
    @if ($title) aria-labelledby="{{ $titleId }}" @endif
    {{ $attributes->class(['modal']) }}>
    <div class="modal-box">
        <div @class(['flex items-center gap-4', 'justify-between' => $title, 'justify-end' => ! $title])>
            @if ($title)
                <h3 id="{{ $titleId }}" class="text-lg font-bold">{{ $title }}</h3>
            @endif

            <button type="button" class="btn btn-sm btn-circle btn-ghost -me-2 shrink-0 text-base" aria-label="Close"
                x-on:click="open = false">&times;</button>
        </div>

        <div class="py-4">
            {{ $slot }}
        </div>

        @isset($actions)
            <div class="modal-action">
                {{ $actions }}
            </div>
        @endisset
    </div>

    <form method="dialog" class="modal-backdrop">
        <button aria-label="Close">close</button>
    </form>
</dialog>
