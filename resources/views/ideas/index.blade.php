<x-layout title="Ideas">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Ideas</h1>
        <x-ui.button variant="primary" :href="route('ideas.create')">New Idea</x-ui.button>
    </div>

    @if ($ideas->isEmpty())
        <p>No ideas yet.</p>
    @else
        <div class="flex flex-col gap-3">
            @foreach ($ideas as $idea)
                <x-card>
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold">{{ $idea->title }}</p>
                            <p>{{ $idea->description }}</p>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <x-ui.button size="sm" :href="route('ideas.show', $idea)">View</x-ui.button>
                            <x-ui.button size="sm" :href="route('ideas.edit', $idea)">Edit</x-ui.button>
                            <form action="{{ route('ideas.destroy', $idea) }}" method="POST"
                                onsubmit="return confirm('Delete this idea?');">
                                @csrf
                                @method('DELETE')
                                <x-ui.button variant="error" size="sm">Delete</x-ui.button>
                            </form>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $ideas->links() }}
        </div>
    @endif
</x-layout>
