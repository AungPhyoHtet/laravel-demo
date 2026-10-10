<x-layout title="Admin">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Admin</h1>
        <p>{{ $userCount }} {{ Str::plural('user', $userCount) }}</p>
    </div>

    @if ($ideas->isEmpty())
        <p>No ideas yet.</p>
    @else
        <div class="flex flex-col gap-3">
            @foreach ($ideas as $idea)
                <x-card>
                    <p>{{ $idea->description_text }}</p>
                    <p class="text-sm opacity-70">by {{ $idea->user->name }}</p>
                </x-card>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $ideas->links() }}
        </div>
    @endif
</x-layout>
