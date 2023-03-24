<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:admin-only, \App\Models\User']);
    }

    public function create()
    {
        $tags = Tag::orderByDesc('id')->get();
        return view('admin.tags.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $this->validatedRequests($request, new Tag);

        Tag::create(["name" => $request->input('name')]);

        return to_route('admin.tags.create')->with('action', "Tag created");
    }

    public function edit(Tag $tag)
    {
        $tags = Tag::orderByDesc('id')->get();
        return view('admin.tags.edit', compact('tags', 'tag'));
    }

    public function update(Request $request, Tag $tag)
    {
         $this->validatedRequests($request, $tag);

        if($tag->update(['name' => $request->input('name')]))
        return to_route('admin.tags.create')->with('action', "Tag Updated");
    }

    public function delete(Tag $tag)
    {
        if($tag->delete())
        return to_route('admin.tags.create')->with('action', "Tag deleted");
    }

    private function validatedRequests(Request $request, $tag)
    {
        return $request->validate([
            'name' => "required|unique:tags,name,$tag->id|regex:/^[a-zA-Z0-9]+$/",
        ], [
            'name.required' => ':attribute is required',
            'name.unique' => ':attribute has been taken',
            'name.regex' => ':attribute must need to be letter contained',
        ], [
            'name' => 'Tags'
        ]);
    }
}
