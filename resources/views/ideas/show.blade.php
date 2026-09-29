<x-layout :title="$idea->title ?: 'Idea'">
    <div class="flex items-center justify-between gap-4 mb-4">
        <h1 class="text-2xl font-bold">{{ $idea->title ?: 'Idea' }}</h1>
        <x-ui.badge variant="primary" outline>{{ $idea->status->label() }}</x-ui.badge>
    </div>

    <x-card>
        @if ($idea->image_path)
            <img src="{{ Storage::disk('public')->url($idea->image_path) }}" alt="Image for {{ $idea->title }}"
                class="max-h-64 w-full rounded-box object-cover">
        @endif

        <p>{{ $idea->description }}</p>

        @if ($idea->links)
            <h2 class="font-semibold mt-2">Links</h2>
            <ul class="list-disc ps-5">
                @foreach ($idea->links as $link)
                    <li><a href="{{ $link }}" class="link link-primary" target="_blank" rel="noopener noreferrer">{{ $link }}</a></li>
                @endforeach
            </ul>
        @endif

        @if ($idea->steps->isNotEmpty())
            <h2 class="font-semibold mt-2">Steps</h2>
            <ul class="list-disc ps-5">
                @foreach ($idea->steps as $step)
                    <li @class(['line-through opacity-70' => $step->is_completed])>{{ $step->description }}</li>
                @endforeach
            </ul>
        @endif
    </x-card>

    <div class="flex gap-2 mt-4">
        <x-ui.button :href="route('ideas.edit', $idea)">Edit</x-ui.button>
        <x-ui.button :href="route('ideas.index')">Back to Ideas</x-ui.button>
    </div>
</x-layout>
