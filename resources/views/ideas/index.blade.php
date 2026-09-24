<x-layout title="Ideas">
    <div class="flex items-center justify-between mt-6 mb-4">
        <h1 class="text-2xl font-bold">Ideas</h1>
        <a href="{{ route('ideas.create') }}" class="btn btn-primary">New Idea</a>
    </div>

    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    @if ($ideas->isEmpty())
        <p>No ideas yet.</p>
    @else
        <div class="flex flex-col gap-3">
            @foreach ($ideas as $idea)
                <x-card class="text-left">
                    <div class="flex items-start justify-between gap-4">
                        <p>{{ $idea->description }}</p>
                        <div class="flex gap-2 shrink-0">
                            <a href="{{ route('ideas.show', $idea) }}" class="btn btn-sm">View</a>
                            <a href="{{ route('ideas.edit', $idea) }}" class="btn btn-sm">Edit</a>
                            <form action="{{ route('ideas.destroy', $idea) }}" method="POST"
                                onsubmit="return confirm('Delete this idea?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-error">Delete</button>
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
