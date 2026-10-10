<x-layout title="Edit Profile">
    <x-ui.form :action="route('profile.update')" method="PATCH" title="Edit Profile" description="Update your account details.">
        <x-ui.input name="name" label="Name" :value="$user->name" />
        <x-ui.input name="email" type="email" label="Email" :value="$user->email" />
        <x-ui.input name="current_password" type="password" label="Current Password" hint="Required only to change your password." />
        <x-ui.input name="password" type="password" label="New Password" hint="Leave blank to keep your current password." />
        <x-ui.input name="password_confirmation" type="password" label="Confirm New Password" />

        <div class="mt-4 flex justify-center gap-2">
            <x-ui.button variant="primary">Save</x-ui.button>
            <x-ui.button :href="route('home')">Cancel</x-ui.button>
        </div>
    </x-ui.form>
</x-layout>
