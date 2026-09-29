<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\Jobs\SendIdeaPublishedNotification;
use App\Models\Idea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $ideas = $request->user()->ideas()->latest()->paginate(10);

        return view('ideas.index', ['ideas' => $ideas]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('ideas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIdeaRequest $request): RedirectResponse
    {
        $idea = DB::transaction(function () use ($request): Idea {
            $idea = $request->user()->ideas()->create([
                ...$request->safe()->only(['title', 'description', 'status', 'links']),
                'image_path' => $request->file('image')?->store('ideas', 'public'),
            ]);

            $idea->steps()->createMany($request->validated('steps', []));

            return $idea;
        });

        dispatch(new SendIdeaPublishedNotification($idea));

        return to_route('ideas.index')->with('success', 'Idea created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Idea $idea): View
    {
        Gate::authorize('view', $idea);

        return view('ideas.show', ['idea' => $idea->load('steps')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Idea $idea): View
    {
        Gate::authorize('update', $idea);

        return view('ideas.edit', ['idea' => $idea->load('steps')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIdeaRequest $request, Idea $idea): RedirectResponse
    {
        $attributes = $request->safe()->only(['title', 'description', 'status', 'links']);
        $previousImagePath = null;

        if ($request->hasFile('image')) {
            $previousImagePath = $idea->image_path;
            $attributes['image_path'] = $request->file('image')->store('ideas', 'public');
        }

        DB::transaction(function () use ($idea, $attributes, $request): void {
            $idea->update($attributes);
            $idea->syncSteps($request->validated('steps', []));
        });

        if ($previousImagePath) {
            Storage::disk('public')->delete($previousImagePath);
        }

        return to_route('ideas.index')->with('success', 'Idea updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea): RedirectResponse
    {
        Gate::authorize('delete', $idea);

        $idea->delete();

        if ($idea->image_path) {
            Storage::disk('public')->delete($idea->image_path);
        }

        return to_route('ideas.index')->with('success', 'Idea deleted.');
    }
}
