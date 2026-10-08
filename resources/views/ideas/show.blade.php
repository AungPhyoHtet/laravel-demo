@php
    $completedStepCount = $idea->steps->where('is_completed', true)->count();
@endphp

<x-layout :title="$idea->title ?: 'Idea'">
    <x-ui.button variant="ghost" size="sm" :href="route('ideas.index')" class="mb-2 -ms-3">
        &larr; Back to Ideas
    </x-ui.button>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold">{{ $idea->title ?: 'Idea' }}</h1>
            <x-ui.badge :variant="$idea->status->badgeVariant()">{{ $idea->status->label() }}</x-ui.badge>
        </div>

        <div class="flex gap-2" x-data>
            <x-ui.button size="sm" :href="route('ideas.edit', $idea)">Edit</x-ui.button>
            <x-ui.button type="button" variant="error" size="sm"
                x-on:click="$dispatch('open-modal', 'delete-idea')">Delete</x-ui.button>
        </div>
    </div>

    <x-ui.modal name="delete-idea" title="Delete this idea?">
        <p>"{{ $idea->title ?: 'Untitled idea' }}" and its steps will be permanently deleted.</p>

        <x-slot:actions>
            <x-ui.button type="button" x-on:click="open = false">Cancel</x-ui.button>
            <form action="{{ route('ideas.destroy', $idea) }}" method="POST">
                @csrf
                @method('DELETE')
                <x-ui.button variant="error">Delete</x-ui.button>
            </form>
        </x-slot:actions>
    </x-ui.modal>

    <x-card>
        @if ($idea->image_path)
            <a href="{{ Storage::disk('public')->url($idea->image_path) }}" target="_blank" rel="noopener noreferrer">
                <img src="{{ Storage::disk('public')->url($idea->image_path) }}" alt="Image for {{ $idea->title }}"
                    class="max-h-96 w-full rounded-box object-cover">
            </a>
        @endif

        <p class="text-sm opacity-70">
            Created {{ $idea->created_at->toFormattedDayDateString() }}
            @if ($idea->updated_at->isAfter($idea->created_at))
                &middot; Updated {{ $idea->updated_at->diffForHumans() }}
            @endif
        </p>

        <section>
            <h2 class="font-semibold mb-1">Description</h2>
            <p class="whitespace-pre-line">{{ $idea->description }}</p>
        </section>

        <section>
            <h2 class="font-semibold mb-1">Links</h2>
            @if ($idea->links)
                <ul class="list-disc ps-5">
                    @foreach ($idea->links as $link)
                        <li class="break-all">
                            <a href="{{ $link }}" class="link link-primary" target="_blank"
                                rel="noopener noreferrer">{{ $link }}</a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm opacity-70">No links added.</p>
            @endif
        </section>

        <section class="flex flex-col gap-1">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">Steps</h2>
                @if ($idea->steps->isNotEmpty())
                    <span class="text-sm opacity-70">{{ $completedStepCount }} / {{ $idea->steps->count() }} done</span>
                @endif
            </div>
            @if ($idea->steps->isNotEmpty())
                <progress class="progress progress-primary" value="{{ $completedStepCount }}"
                    max="{{ $idea->steps->count() }}"
                    aria-label="{{ $completedStepCount }} of {{ $idea->steps->count() }} steps done"></progress>
                <ul class="flex flex-col gap-1">
                    @foreach ($idea->steps as $step)
                        <li>
                            <form action="{{ route('steps.update', $step) }}" method="POST" x-data>
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_completed" value="0">
                                <x-ui.checkbox name="is_completed" :id="'step-'.$step->id" :checked="$step->is_completed"
                                    variant="primary" size="sm" x-on:change="$el.form.requestSubmit()">
                                    <span @class(['line-through opacity-70' => $step->is_completed])>{{ $step->description }}</span>
                                </x-ui.checkbox>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm opacity-70">No steps added.</p>
            @endif
        </section>
    </x-card>
</x-layout>
