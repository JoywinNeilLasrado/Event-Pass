<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminTagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('events')->latest()->get();
        return view('admin.tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100|unique:tags,name']);
        Tag::create(['name' => $data['name'], 'slug' => Str::slug($data['name'])]);
        return back()->with('success', 'Tag created.');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return back()->with('success', "Tag \"{$tag->name}\" deleted.");
    }
}
