@props([
    'idea' => null,
])

@php
    $links = old('links', $idea?->links ?? []);
    $steps = old('steps', $idea?->steps->map->only(['id', 'description'])->all() ?? []);
    $linkErrors = [...$errors->get('links'), ...collect($errors->get('links.*'))->flatten()];
    $stepErrors = [...$errors->get('steps'), ...collect($errors->get('steps.*'))->flatten()];
@endphp

<x-ui.input name="title" label="Title" :value="$idea?->title" />

<x-ui.textarea name="description" label="Description" rows="4" :value="$idea?->description" hint="Supports Markdown: **bold**, _italic_, # headings, - lists and [links](https://example.com)." />

<x-ui.select name="status" label="Status" :options="\App\Enums\IdeaStatus::options()"
    :value="$idea?->status ?? \App\Enums\IdeaStatus::Pending" />

<fieldset class="fieldset" x-data="{ links: {{ Js::from($links ?: ['']) }} }">
    <legend class="fieldset-legend">Links</legend>

    <template x-for="(link, index) in links" :key="index">
        <div class="flex gap-2">
            <input type="url" name="links[]" x-model="links[index]" placeholder="https://example.com"
                aria-label="Link" class="input w-full">
            <button type="button" class="btn btn-ghost btn-square" aria-label="Remove link"
                x-on:click="links.splice(index, 1)">&times;</button>
        </div>
    </template>

    <x-ui.button type="button" size="sm" class="self-start" x-on:click="links.push('')">Add link</x-ui.button>

    <x-input-error :messages="$linkErrors" />
</fieldset>

<fieldset class="fieldset"
    x-data="{ steps: {{ Js::from($steps ?: [['id' => null, 'description' => '']]) }} }">
    <legend class="fieldset-legend">Steps</legend>

    <template x-for="(step, index) in steps" :key="index">
        <div class="flex gap-2">
            <input type="hidden" :name="`steps[${index}][id]`" :value="step.id">
            <input type="text" :name="`steps[${index}][description]`" x-model="step.description"
                placeholder="Describe a step" aria-label="Step" class="input w-full">
            <button type="button" class="btn btn-ghost btn-square" aria-label="Remove step"
                x-on:click="steps.splice(index, 1)">&times;</button>
        </div>
    </template>

    <x-ui.button type="button" size="sm" class="self-start"
        x-on:click="steps.push({ id: null, description: '' })">Add step</x-ui.button>

    <x-input-error :messages="$stepErrors" />
</fieldset>

<fieldset class="fieldset">
    <legend class="fieldset-legend">Image</legend>

    @if ($idea?->image_path)
        <img src="{{ Storage::disk('public')->url($idea->image_path) }}" alt="Current image for {{ $idea->title }}"
            class="mb-2 max-h-40 w-fit rounded-box object-cover">
    @endif

    <input type="file" id="image" name="image" accept="image/*"
        @class(['file-input w-full', 'file-input-error' => $errors->has('image')])>
    <p class="label">JPG, PNG, GIF or WebP up to 2 MB.</p>

    <x-input-error :messages="$errors->get('image')" />
</fieldset>
