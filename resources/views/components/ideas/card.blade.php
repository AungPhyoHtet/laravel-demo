@props([
    'idea',
])

@php
    $completedStepCount = $idea->steps->where('is_completed', true)->count();
@endphp

<article {{ $attributes->class(['card bg-base-200 border border-base-300']) }}>
    @if ($idea->image_path)
        <figure>
            <img src="{{ Storage::disk('public')->url($idea->image_path) }}" alt="Image for {{ $idea->title }}"
                class="h-40 w-full object-cover">
        </figure>
    @endif

    <div class="card-body">
        <div class="flex items-start justify-between gap-2">
            <h2 class="card-title">
                <a href="{{ route('ideas.show', $idea) }}" class="link link-hover">{{ $idea->title ?: 'Untitled idea' }}</a>
            </h2>
            <x-ui.badge :variant="$idea->status->badgeVariant()" size="sm" class="shrink-0">
                {{ $idea->status->label() }}
            </x-ui.badge>
        </div>

        <p class="text-sm opacity-70">Created {{ $idea->created_at->diffForHumans() }}</p>

        <p class="whitespace-pre-line">{{ $idea->description_text }}</p>

        @if ($idea->links)
            <div>
                <h3 class="text-sm font-semibold">Links</h3>
                <ul class="text-sm">
                    @foreach ($idea->links as $link)
                        <li class="truncate">
                            <a href="{{ $link }}" class="link link-primary" target="_blank"
                                rel="noopener noreferrer">{{ $link }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($idea->steps->isNotEmpty())
            <div class="flex flex-col gap-1">
                <div class="flex items-center justify-between text-sm">
                    <h3 class="font-semibold">Steps</h3>
                    <span class="opacity-70">{{ $completedStepCount }} / {{ $idea->steps->count() }} done</span>
                </div>
                <progress class="progress progress-primary" value="{{ $completedStepCount }}"
                    max="{{ $idea->steps->count() }}"
                    aria-label="{{ $completedStepCount }} of {{ $idea->steps->count() }} steps done"></progress>
                <ul class="list-disc ps-5 text-sm">
                    @foreach ($idea->steps as $step)
                        <li @class(['line-through opacity-70' => $step->is_completed])>{{ $step->description }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card-actions mt-auto justify-end pt-2">
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
</article>
