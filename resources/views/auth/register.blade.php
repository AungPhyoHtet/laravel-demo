<x-layout title="Register">
    <div class="flex flex-col items-center">
        <form action="{{ route('register') }}" method="POST" class="w-full max-w-sm mt-6">
            @csrf

            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-4">
                <legend class="fieldset-legend">Register</legend>

                <label for="name" class="label">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    class="input w-full">
                <x-input-error :messages="$errors->get('name')" />

                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    class="input w-full">
                <x-input-error :messages="$errors->get('email')" />

                <label for="password" class="label">Password</label>
                <input id="password" name="password" type="password" required class="input w-full">
                <x-input-error :messages="$errors->get('password')" />

                <label for="password_confirmation" class="label">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="input w-full">

                <div class="flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Register</button>
                    <a href="{{ route('home') }}" class="btn">Cancel</a>
                </div>
            </fieldset>
        </form>
    </div>
</x-layout>
