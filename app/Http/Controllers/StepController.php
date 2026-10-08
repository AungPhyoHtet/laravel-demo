<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStepRequest;
use App\Models\Step;
use Illuminate\Http\RedirectResponse;

class StepController extends Controller
{
    /**
     * Update the completion status of the specified step.
     */
    public function update(UpdateStepRequest $request, Step $step): RedirectResponse
    {
        $step->update(['is_completed' => $request->boolean('is_completed')]);

        return to_route('ideas.show', $step->idea_id);
    }
}
