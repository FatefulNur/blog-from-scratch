<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:admin-only, \App\Models\User']);
    }

    public function index()
    {
        $comments = Comment::all();
        return view('admin.comments.index', compact('comments'));
    }

    public function store(Request $request)
    {
        $this->validatedRequests($request);

        $depth = 0;
        if ($request->has('parent_id')) {
            $parent = Comment::find($request->input('parent_id'));
            if ($parent) {
                $depth = ($parent->depth + 1);
            }
        }
        $comment = Comment::create([
            "user_id" => $request->input('user_id'),
            "blog_id" => $request->input('blog_id'),
            "parent_id" => $request->input('parent_id'),
            "body" => $request->input('body'),
            "depth" => $depth,
            "status" => CommentStatus::PUBLISHED
        ]);

        if ($request->hasFile('photo')) {

            $upload = $request->file('photo');
            $photo_path = 'uploads/comment';
            $photo_name = $upload->hashName($photo_path);

            $upload->move(public_path('uploads/comment'), $photo_name);

            $comment->image()->create([
                'path' => $photo_name
            ]);

            session()->flash('image', 'image created');
        }

        return to_route('admin.comments.index')->with('action', 'Comment created');
    }

    public function delete(Comment $comment)
    {
        if($comment->image) {
            $comment->image()->delete();
            if(file_exists(public_path($comment->image->path))) {
                File::delete(public_path($comment->image->path));
            }
        }

        if ($comment->delete())
            return to_route('admin.comments.index')->with('action', 'Comment deleted');
    }

    public function edit(Comment $comment)
    {
        return view('admin.comments.edit', compact('comment'));
    }

    public function update(Request $request, Comment $comment)
    {
        $this->validatedRequests($request);

        $comment->update([
            "user_id" => $request->input('user_id'),
            "blog_id" => $request->input('blog_id'),
            "parent_id" => $request->input('parent_id'),
            "body" => $request->input('body'),
            "status" => $request->input('status')
        ]);

        if ($request->hasFile('photo')) {

            $upload = $request->file('photo');
            $photo_path = 'uploads/comment';

            if (file_exists(public_path($comment->image->path))) {
                File::delete(public_path($comment->image->path));
            }

            $photo_name = $upload->hashName($photo_path);
            $upload->move(public_path('uploads/comment'), $photo_name);

            $comment->image()->update([
                'path' => $photo_name
            ]);
        }

        return to_route('admin.comments.index')->with('action', 'Comment updated');
    }

    private function validatedRequests(Request $request)
    {
        return $request->validate([
            'photo' => 'mimes:png,jpg,jpeg,gif|max:2048',
            'body' => 'required|min:5'
        ]);
    }
}
