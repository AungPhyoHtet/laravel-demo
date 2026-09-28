<x-layout title="Log In">
    <x-ui.form :action="route('login')" title="Log In" description="Welcome back. Enter your email and password to continue.">
        <x-ui.input name="email" type="email" label="Email" placeholder="you@example.com" autofocus />
        <x-ui.input name="password" type="password" label="Password" />
        <x-ui.checkbox name="remember" label="Remember me" size="sm" />

        <div class="mt-4 flex justify-center gap-2">
            <x-ui.button variant="primary">Log In</x-ui.button>
            <x-ui.button :href="route('home')">Cancel</x-ui.button>
        </div>
    </x-ui.form>
</x-layout>
