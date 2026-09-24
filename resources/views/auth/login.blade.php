<x-layout title="Log In">
    <div class="flex flex-col items-center">
        <form action="{{ route('login') }}" method="POST" class="w-full max-w-sm mt-6">
            @csrf

            <fieldset class="fieldset bg-base-200 border-base-300 rounded-box border p-4">
                <legend class="fieldset-legend">Log In</legend>

                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                    class="input w-full">
                <x-input-error :messages="$errors->get('email')" />

                <label for="password" class="label">Password</label>
                <input id="password" name="password" type="password" required class="input w-full">
                <x-input-error :messages="$errors->get('password')" />

                <label class="label">
                    <input type="checkbox" name="remember" class="checkbox checkbox-sm">
                    Remember me
                </label>

                <div class="flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Log In</button>
                    <a href="{{ route('home') }}" class="btn">Cancel</a>
                </div>
            </fieldset>
        </form>
    </div>
</x-layout>
