<x-layout title="Register">
    <x-ui.form :action="route('register')" title="Register" description="Create an account to start sharing your ideas.">
        <x-ui.input name="name" label="Name" placeholder="Jane Doe" autofocus />
        <x-ui.input name="email" type="email" label="Email" placeholder="you@example.com" />
        <x-ui.input name="password" type="password" label="Password" hint="Use at least 8 characters." />
        <x-ui.input name="password_confirmation" type="password" label="Confirm Password" />

        <div class="mt-4 flex justify-center gap-2">
            <x-ui.button variant="primary">Register</x-ui.button>
            <x-ui.button :href="route('home')">Cancel</x-ui.button>
        </div>
    </x-ui.form>
</x-layout>
