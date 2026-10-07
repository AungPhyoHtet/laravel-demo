<?php

use App\Enums\IdeaStatus;

beforeEach(function (): void {
    request()->setLaravelSession(session()->driver());
    $this->withViewErrors([]);
});

it('renders a button as a link when given an href', function (): void {
    $this->blade('<x-ui.button variant="primary" href="/ideas">Ideas</x-ui.button>')
        ->assertSee('<a href="/ideas" class="btn btn-primary">Ideas</a>', false);

    $this->blade('<x-ui.button variant="error" size="sm">Delete</x-ui.button>')
        ->assertSee('<button type="submit" class="btn btn-error btn-sm">Delete</button>', false);
});

it('repopulates old input and shows validation errors on inputs', function (): void {
    session()->flashInput(['email' => 'old@example.com', 'password' => 'secret-value']);

    $this->withViewErrors(['email' => 'The email field is invalid.'])
        ->blade('<x-ui.input name="email" type="email" label="Email" /><x-ui.input name="password" type="password" />')
        ->assertSee('value="old@example.com"', false)
        ->assertSee('input-error', false)
        ->assertSee('<p class="label text-error">The email field is invalid.</p>', false)
        ->assertDontSee('secret-value');
});

it('prefers old input over the given value for textareas', function (): void {
    session()->flashInput(['description' => 'Edited description']);

    $this->blade('<x-ui.textarea name="description" value="Saved description" />')
        ->assertSee('Edited description')
        ->assertDontSee('Saved description');
});

it('checks checkboxes and radios from old input after a failed submission', function (): void {
    session()->flashInput(['status' => IdeaStatus::Completed->value]);

    $this->blade(
        '<x-ui.checkbox name="remember" :checked="true" /><x-ui.radio-group name="status" :options="$options" :value="$value" />',
        [
            'options' => ['pending' => 'Pending', 'completed' => 'Completed'],
            'value' => IdeaStatus::Pending,
        ],
    )
        ->assertDontSee('name="remember" type="checkbox" value="1" checked', false)
        ->assertSee('value="completed" checked', false)
        ->assertDontSee('value="pending" checked', false);
});

it('selects the option matching an enum value', function (): void {
    $this->blade(
        '<x-ui.select name="status" :options="$options" :value="$value" />',
        [
            'options' => ['pending' => 'Pending', 'in_progress' => 'In Progress'],
            'value' => IdeaStatus::InProgress,
        ],
    )->assertSee('<option value="in_progress" selected', false);
});

it('renders a form with a title, description, csrf token and spoofed method', function (): void {
    $this->blade('<x-ui.form action="/ideas/1" method="put" title="Edit Idea" description="Update your idea.">Fields</x-ui.form>')
        ->assertSee('<h1 class="text-2xl font-bold">Edit Idea</h1>', false)
        ->assertSee('Update your idea.')
        ->assertSee('action="/ideas/1" method="POST" novalidate', false)
        ->assertSee('name="_token"', false)
        ->assertSee('name="_method" value="PUT"', false)
        ->assertSee('Fields');
});

it('shows a hint below an input', function (): void {
    $this->blade('<x-ui.input name="password" type="password" label="Password" hint="Use at least 8 characters." />')
        ->assertSee('<legend class="fieldset-legend">Password</legend>', false)
        ->assertSee('<p class="label">Use at least 8 characters.</p>', false);
});

it('renders flashed success and error messages as dismissible alerts', function (): void {
    session()->flash('success', 'Idea created.');
    session()->flash('error', 'Something went wrong.');

    $this->blade('<x-ui.flash />')
        ->assertSeeInOrder(['alert-success', 'Idea created.', 'alert-error', 'Something went wrong.'], false)
        ->assertSee('x-on:click="visible = false"', false);
});

it('renders nothing when no message is flashed', function (): void {
    $this->blade('<x-ui.flash />')->assertDontSee('toast', false);
});

it('renders textareas and selects with a legend and a hint that errors replace', function (): void {
    $this->blade('<x-ui.textarea name="description" label="Description" hint="At least 10 characters." />')
        ->assertSee('<legend class="fieldset-legend">Description</legend>', false)
        ->assertSee('<p class="label">At least 10 characters.</p>', false);

    $this->withViewErrors(['status' => 'The status field is required.'])
        ->blade('<x-ui.select name="status" label="Status" hint="Pick one." :options="[]" />')
        ->assertSee('<legend class="fieldset-legend">Status</legend>', false)
        ->assertSee('The status field is required.')
        ->assertDontSee('Pick one.');
});

it('renders a radio group as a fieldset with a legend and a hint', function (): void {
    $this->blade('<x-ui.radio-group name="status" label="Status" hint="Pick one." :options="[\'pending\' => \'Pending\']" />')
        ->assertSee('<fieldset class="fieldset">', false)
        ->assertSee('<legend class="fieldset-legend">Status</legend>', false)
        ->assertSee('<p class="label">Pick one.</p>', false);
});

it('renders a modal labelled by its title with an actions footer', function (): void {
    $this->blade('<x-ui.modal name="delete-idea" title="Delete this idea?">Body<x-slot:actions>Confirm</x-slot:actions></x-ui.modal>')
        ->assertSee('aria-labelledby="delete-idea-modal-title"', false)
        ->assertSee('<h3 id="delete-idea-modal-title" class="text-lg font-bold">Delete this idea?</h3>', false)
        ->assertSeeInOrder(['Body', 'modal-action', 'Confirm'], false);
});

it('renders a modal closed by default and open when shown', function (): void {
    $this->blade('<x-ui.modal name="info">Body</x-ui.modal>')
        ->assertSee('x-data="{ open: false }"', false)
        ->assertDontSee('aria-labelledby', false);

    $this->blade('<x-ui.modal name="info" :show="true">Body</x-ui.modal>')
        ->assertSee('x-data="{ open: true }"', false);
});
