<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:admin-only, \App\Models\User']);
    }

    public function index()
    {
        $images = Image::all();
        return view('admin.media.index', compact('images'));
    }

    public function show(Image $image)
    {
        return view('admin.media.show', compact('image'));
    }

    public function edit(Image $image)
    {
        return view('admin.media.edit', compact('image'));
    }

    public function update(Request $request, Image $image)
    {
        $image->update($request->only(['name', 'caption', 'details']));

        return back()->with('action', "Image updated");
    }
}
