<x-layout title="Idea">
    <h1 class="text-2xl font-bold mb-4">Idea</h1>

    <x-card>
        <p>{{ $idea->description }}</p>
    </x-card>

    <div class="flex gap-2 mt-4">
        <x-ui.button :href="route('ideas.edit', $idea)">Edit</x-ui.button>
        <x-ui.button :href="route('ideas.index')">Back to Ideas</x-ui.button>
    </div>
</x-layout>
