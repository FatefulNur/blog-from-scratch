<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Tag;
use App\Traits\Controller\BlogCacheControl;
use App\Traits\Controller\BlogManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BlogsController extends Controller
{
    use BlogManagement, BlogCacheControl;

    public function __construct()
    {
        $this->middleware(['auth', 'can:admin-only, \App\Models\User']);
    }

    public function index()
    {
        $blogs = Cache::get(self::$cache_blogs);
        $thumbnails = Cache::get(self::$cache_blog_thumbs);
        $trashes = Cache::get(self::$cache_only_trashed_blogs);

        return view('admin.blogs.index', compact('blogs', 'thumbnails', 'trashes'));
    }

    public function trashed()
    {
        $trashes = Cache::get(self::$cache_only_trashed_blogs);
        return view('admin.blogs.trash', compact('trashes'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.blogs.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $this->validatedRequests($request);

        $category = Category::query()->firstOrCreate(['name' => 'Uncategorized'], [
            'depth' => 0,
        ]);

        $blog = Blog::create([
            'title' => $request->input('title'),
            'slug' => Str::uuid()->toString(),
            'short_desc' => $request->input('short_desc'),
            'category_id' => $request->input('parent_id') ?: $category->id,
            'description' => $request->input('description'),
            'user_id' => $request->input('user_id'),
            'can_commented' => is_null($request->can_commented) ? 0 : 1,
            'featured' => ($request->input('featured') == "on") ? 1 : 0
        ]);

        // storing tags
        if(!is_null($request->name)) {
            $tags = collect(explode(",", $request->name))->map(fn($item) => Tag::query()->firstOrCreate(['name' => $item]));
            $blog->tags()->sync($tags->pluck('id')->toArray());
        }

        $this->processForThumnailStoring($request, $blog);

        $this->processForGalleryStoring($request, $blog);

        $this->reCachedBlogs();

        if (!empty($blog)) {
            return redirect('/admin/blogs')->with('action', 'Blog Created');
        }
    }

    public function delete(Blog $blog)
    {
        if ($blog->delete()) {
            $this->reCachedOnlyTrashedBlogs();
            $this->reCachedBlogs();

            return back()->with('action', 'Blog moves to trash');
        }
    }

    public function removeGallery(Blog $blog)
    {
        if (!($blog->gallery->blog_id > 0)) {
            return;
        }

        $blog->gallery->images->each(function ($item) {
            if (!file_exists(public_path($item->path))) {
                return;
            }
            File::delete(public_path($item->path));
        });

        $blog->gallery->images->each->delete();
        $blog->gallery()->delete();

        return back()->with('success', 'gallery removed');
    }

    public function removeThumbnail(Blog $blog)
    {
        if (empty($blog->image)) {
            return;
        }

        if (file_exists(public_path($blog->image->path))) {
            File::delete(public_path($blog->image->path));
        }

        $blog->image()->delete();

        return back()->with('success', 'image removed');
    }

    public function edit(Blog $blog)
    {
        $gallery = $blog->gallery->images;
        $categories = Category::all();
        $allow_comment = Setting::first()->allow_comment ?? 0;
        return view('admin.blogs.edit', compact('blog', 'gallery', 'categories', 'allow_comment'));
    }

    public function update(Request $request, Blog $blog)
    {
        $this->validatedRequests($request);

        $updated = $blog->update([
            'title' => $request->input('title'),
            'slug' => $request->input('slug'),
            'short_desc' => $request->input('short_desc'),
            'description' => $request->input('description'),
            'user_id' => $request->input('user_id'),
            'category_id' => $request->input('parent_id'),
            'can_commented' => is_null($request->can_commented) ? 0 : 1,
            'featured' => ($request->input('featured') == "on") ? 1 : 0

        ]);

        // updating tag option
        if(!is_null($request->name)) {
            $tags = collect(explode(",", $request->input('name')))->map(fn($item) => Tag::firstOrCreate(['name' => $item]));
            $blog->tags()->sync($tags->pluck('id')->toArray());
        } else {
            $blog->tags()->detach();
        }

        $this->processForThumbnailUpdting($request, $blog);

        $this->processForGalleryUpdting($request, $blog);

        if ($updated) {
            $this->reCachedBlogs();
            $this->reCachedThumbnails();
            $this->reCachedGalleryImages();

            return redirect('admin/blogs')->with('action', 'Blog updated');
        }
    }

    public function forceDelete(Blog $blog)
    {
        $this->reCachedOnlyTrashedBlogs();

        if (!empty($blog->image)) {
            if (file_exists(public_path($blog->image->path))) {
                File::delete(public_path($blog->image->path));
            }
            $blog->image()->delete();
            session()->flash('image', 'image permanently deleted');
        }

        if (!empty($blog->gallery)) {
            $blog->gallery->images->each(function ($item) {
                if (file_exists(public_path($item->path))) {
                    File::delete(public_path($item->path));
                }
                return;
            });

            $blog->gallery->images->each->delete();
            $blog->gallery()->delete();
            session()->flash('gallery', 'gallery permanently deleted');
        }

        if ($blog->forceDelete()) {
            $this->reCachedOnlyTrashedBlogs();
            $this->reCachedBlogs();

            return back()->with('action', 'The Blog has been deleted permanently');
        }
    }

    public function emptyTrash()
    {
        $blog = Blog::onlyTrashed()->get();

        $this->processForDeletingImageFromBin($blog);

        $this->processForDeletingGalleryFromBin($blog);

        $blog->each->forceDelete();

        Cache::forget(self::$cache_only_trashed_blogs);

        return back()->with('action', 'You have an empty trash');
    }

    public function restore(Blog $blog)
    {
        $this->reCachedOnlyTrashedBlogs();

        if ($blog->restore()) {
            $this->reCachedOnlyTrashedBlogs();

            $this->reCachedBlogs();

            return back()->with('action', 'Blog Restored');
        }
    }

    public function restoreAll()
    {
        Blog::onlyTrashed()->get()->each->restore();

        Cache::forget(self::$cache_only_trashed_blogs);

        $this->reCachedBlogs();

        return back()->with('action', 'All Blog restored');
    }
}
