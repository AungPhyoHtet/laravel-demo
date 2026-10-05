<x-layout title="Ideas">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Ideas</h1>
        <x-ui.button variant="primary" :href="route('ideas.create')">New Idea</x-ui.button>
    </div>

    @error('status')
        <x-ui.alert variant="error" class="mb-4">{{ $message }}</x-ui.alert>
    @enderror

    <nav aria-label="Filter ideas by status" class="mb-4 flex flex-wrap gap-2">
        <x-ui.button variant="primary" size="sm" :outline="(bool) $status" :href="route('ideas.index')"
            :aria-current="$status ? false : 'page'">
            All
            <span class="badge badge-sm">{{ $statusCounts->sum() }}</span>
        </x-ui.button>
        @foreach (App\Enums\IdeaStatus::cases() as $filterStatus)
            <x-ui.button variant="primary" size="sm" :outline="$status !== $filterStatus"
                :href="route('ideas.index', ['status' => $filterStatus])"
                :aria-current="$status === $filterStatus ? 'page' : false">
                {{ $filterStatus->label() }}
                <span class="badge badge-sm">{{ $statusCounts->get($filterStatus->value, 0) }}</span>
            </x-ui.button>
        @endforeach
    </nav>

    @if ($ideas->isEmpty())
        <p>{{ $status ? 'No ' . strtolower($status->label()) . ' ideas.' : 'No ideas yet.' }}</p>
    @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($ideas as $idea)
                <x-ideas.card :idea="$idea" />
            @endforeach
        </div>

        <div class="mt-4">
            {{ $ideas->links() }}
        </div>
    @endif
</x-layout>
