<x-layout title="New Idea">
    <h1 class="text-2xl font-bold mb-4">New Idea</h1>

    <form action="{{ route('ideas.store') }}" method="POST" class="flex flex-col gap-4">
        @csrf

        <x-ui.textarea name="description" label="Description" rows="4" />

        <div class="flex gap-2">
            <x-ui.button variant="primary">Create</x-ui.button>
            <x-ui.button :href="route('ideas.index')">Cancel</x-ui.button>
        </div>
    </form>
</x-layout>
