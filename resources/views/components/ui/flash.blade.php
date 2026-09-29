@php
    $messages = array_filter([
        'success' => session('success'),
        'error' => session('error'),
    ]);
@endphp

@if ($messages)
    <div class="toast z-50">
        @foreach ($messages as $variant => $message)
            <x-ui.alert :variant="$variant" class="shadow-lg" x-data="{ visible: true }"
                x-init="setTimeout(() => visible = false, 5000)" x-show="visible" x-transition.opacity.duration.300ms>
                <span>{{ $message }}</span>
                <button type="button" class="btn btn-ghost btn-xs btn-circle" aria-label="Dismiss"
                    x-on:click="visible = false">&times;</button>
            </x-ui.alert>
        @endforeach
    </div>
@endif
