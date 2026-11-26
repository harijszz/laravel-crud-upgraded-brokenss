<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();

        return view('tags/index', compact('tags'));
    }

    public function create()
    {
        return view('tags/create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|unique:tags,title|max:255',
        ]);

        $tag = Tag::create($validated);

        return redirect()
            ->route('tag/show', [$tag])
            ->with('message', 'Tag created successfully');
    }

    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
        ]);

        $tags = Tag::query()
            ->when($validated['q'] ?? null, function ($query, $q) {
                $query->where('title', 'like', '%'.$q.'%');
            })
            ->orderBy('title')
            ->limit(10)
            ->get(['id', 'title']);

        return response()->json(['tags' => $tags]);
    }

    public function show(Tag $tag)
    {
        return view('tags/show', compact('tag'));
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()
            ->route('tag/index')
            ->with('message', 'Tag deleted successfully');
    }

    public function edit(Tag $tag)
    {
        return view('tags/edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'title' => 'required|unique:tags,title,'.$tag->id.'|max:255',
        ]);

        $tag->update($validated);

        return redirect()
            ->route('tag/show', [$tag])
            ->with('message', 'Tag updated successfully');
    }
}
