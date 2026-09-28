<x-layout title="Edit Idea">
    <h1 class="text-2xl font-bold mb-4">Edit Idea</h1>

    <form action="{{ route('ideas.update', $idea) }}" method="POST" class="flex flex-col gap-4">
        @csrf
        @method('PUT')

        <x-ui.textarea name="description" label="Description" rows="4" :value="$idea->description" />

        <div class="flex gap-2">
            <x-ui.button variant="primary">Update</x-ui.button>
            <x-ui.button :href="route('ideas.index')">Cancel</x-ui.button>
        </div>
    </form>
</x-layout>
