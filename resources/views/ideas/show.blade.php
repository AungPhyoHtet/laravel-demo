<x-layout title="Idea">
    <h1 class="text-2xl font-bold mt-6 mb-4">Idea</h1>

    <x-card class="text-left">
        <p>{{ $idea->description }}</p>
    </x-card>

    <div class="flex gap-2 mt-4">
        <a href="{{ route('ideas.edit', $idea) }}" class="btn">Edit</a>
        <a href="{{ route('ideas.index') }}" class="btn">Back to Ideas</a>
    </div>
</x-layout>
