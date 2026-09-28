<x-layout title="Log In">
    <form action="{{ route('login') }}" method="POST" class="mx-auto max-w-sm">
        @csrf

        <x-ui.fieldset legend="Log In">
            <x-ui.input name="email" type="email" label="Email" required autofocus />
            <x-ui.input name="password" type="password" label="Password" required />
            <x-ui.checkbox name="remember" label="Remember me" size="sm" />

            <div class="flex gap-2 mt-4">
                <x-ui.button variant="primary">Log In</x-ui.button>
                <x-ui.button :href="route('home')">Cancel</x-ui.button>
            </div>
        </x-ui.fieldset>
    </form>
</x-layout>
