<x-layout title="Register">
    <form action="{{ route('register') }}" method="POST" class="mx-auto max-w-sm">
        @csrf

        <x-ui.fieldset legend="Register">
            <x-ui.input name="name" label="Name" required autofocus />
            <x-ui.input name="email" type="email" label="Email" required />
            <x-ui.input name="password" type="password" label="Password" required />
            <x-ui.input name="password_confirmation" type="password" label="Confirm Password" required />

            <div class="flex gap-2 mt-4">
                <x-ui.button variant="primary">Register</x-ui.button>
                <x-ui.button :href="route('home')">Cancel</x-ui.button>
            </div>
        </x-ui.fieldset>
    </form>
</x-layout>
