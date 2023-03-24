<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public $perPage = 18;
    public function index()
    {
        $latestBlogs = Blog::latest()->take(5)->get();
        $oldestBlogs = Blog::oldest()->take(3)->get();
        $featured = Blog::where("featured", true)->get();
        $categories = Category::whereNull("parent_id")->latest()->take(4)->get();
        $postOfCategory = Category::oldest()->take(2)->get();
        $tags = Tag::inRandomOrder()->select('name')->take(20)->get();

        return view('user.home', compact('latestBlogs', 'oldestBlogs', 'featured', 'categories', 'postOfCategory', 'tags'));
    }

    public function blog()
    {
        $latestBlogs = Blog::latest()->take(4)->get();
        $blogs = Blog::query()->paginate($this->perPage);
        $tags = Tag::inRandomOrder()->select('name')->take(20)->get();

        return view('user.blog', compact('latestBlogs', 'blogs', 'tags'));
    }

    public function mountCategory()
    {
        $categories = Category::all();
        $tags = Tag::inRandomOrder()->select('name')->take(20)->get();
        $latestBlogs = Blog::latest()->take(4)->get();
        return view('user.categories', compact('latestBlogs', 'tags', 'categories'));
    }

    public function category($category)
    {
        $segments = explode('/', $category);
        $currentObj = null;
        $parentObj = null;

        foreach ($segments as $segment) {
            if (!$currentObj) {
                // if the category object is not set, find the root category
                $currentObj = Category::where('name', $segment)->whereNull('parent_id')->firstOrFail();
            } else {
                // find the child category for the current parent category
                $parentObj = $currentObj;
                $currentObj = Category::where('name', $segment)->where('parent_id', $parentObj->id)->firstOrFail();
            }
        }

        $category = $currentObj;
        $postsTotal = $this->perPage;
        $tags = Tag::inRandomOrder()->select('name')->take(20)->get();
        $latestBlogs = Blog::latest()->take(4)->get();
        return view('user.category', compact('latestBlogs', 'tags', 'category', 'postsTotal'));
    }

    public function single($category = "Uncategorized", Blog $blog)
    {
        $tags = Tag::inRandomOrder()->select('name')->take(20)->get();
        $latestBlogs = Blog::latest()->take(4)->get();
        return view("user.single", compact('blog', 'latestBlogs', 'tags'));
    }
}
