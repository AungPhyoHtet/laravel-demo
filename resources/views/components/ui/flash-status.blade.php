@if (session('status'))
    <x-ui.alert variant="success" {{ $attributes }}>
        {{ session('status') }}
    </x-ui.alert>
@endif
