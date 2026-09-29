<?php

use App\Models\User;

it('adds and removes link and step rows', function (): void {
    $this->actingAs(User::factory()->create());

    visit(route('ideas.create'))
        ->assertCount('input[name="links[]"]', 1)
        ->assertCount('input[name$="[description]"]', 1)
        ->click('Add link')
        ->click('Add step')
        ->click('Add step')
        ->assertCount('input[name="links[]"]', 2)
        ->assertCount('input[name$="[description]"]', 3)
        ->click('[aria-label="Remove step"] >> nth=0')
        ->assertCount('input[name$="[description]"]', 2)
        ->assertNoJavaScriptErrors();
});
