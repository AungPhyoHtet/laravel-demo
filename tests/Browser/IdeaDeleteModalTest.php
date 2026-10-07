<?php

use App\Models\Idea;
use App\Models\User;

it('opens the delete modal and closes it with cancel, escape and the close button', function (): void {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();

    $this->actingAs($user);

    visit(route('ideas.show', $idea))
        ->assertMissing('dialog[open]')
        ->press('Delete')
        ->assertVisible('dialog[open]')
        ->assertSeeIn('dialog[open]', 'Delete this idea?')
        ->press('Cancel')
        ->assertMissing('dialog[open]')
        ->press('Delete')
        ->keys('dialog[open]', 'Escape')
        ->assertMissing('dialog[open]')
        ->press('Delete')
        ->click('dialog[open] .modal-box [aria-label="Close"]')
        ->assertMissing('dialog[open]')
        ->assertNoJavaScriptErrors();

    $this->assertModelExists($idea);
});

it('deletes the idea when confirmed in the modal', function (): void {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();

    $this->actingAs($user);

    visit(route('ideas.show', $idea))
        ->press('Delete')
        ->click('dialog[open] .modal-action button[type="submit"]')
        ->assertPathIs('/ideas')
        ->assertSee('Idea deleted.')
        ->assertNoJavaScriptErrors();

    $this->assertModelMissing($idea);
});
