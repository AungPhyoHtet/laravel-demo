<x-layout title="New Idea">
    <h1 class="text-2xl font-bold mt-6 mb-4">New Idea</h1>

    <form action="{{ route('ideas.store') }}" method="POST" class="flex flex-col gap-4">
        @csrf

        <div class="form-control">
            <label for="description" class="label">
                <span class="label-text">Description</span>
            </label>
            <textarea id="description" name="description" rows="4"
                class="textarea textarea-bordered w-full">{{ old('description') }}</textarea>
            <x-input-error :messages="$errors->get('description')" class="mt-1" />
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('ideas.index') }}" class="btn">Cancel</a>
        </div>
    </form>
</x-layout>
