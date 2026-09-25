<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\Jobs\SendIdeaPublishedNotification;
use App\Models\Idea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        $idea = $request->user()->ideas()->create($request->validated());

        dispatch(new SendIdeaPublishedNotification($idea));

        return to_route('ideas.index')->with('status', 'Idea created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Idea $idea): View
    {
        Gate::authorize('view', $idea);

        return view('ideas.show', ['idea' => $idea]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Idea $idea): View
    {
        Gate::authorize('update', $idea);

        return view('ideas.edit', ['idea' => $idea]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIdeaRequest $request, Idea $idea): RedirectResponse
    {
        $idea->update($request->validated());

        return to_route('ideas.index')->with('status', 'Idea updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea): RedirectResponse
    {
        Gate::authorize('delete', $idea);

        $idea->delete();

        return to_route('ideas.index')->with('status', 'Idea deleted.');
    }
}
