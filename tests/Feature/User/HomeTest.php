<?php

namespace Tests\Feature\User;

use App\Models\Blog;
use App\Models\Category;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_home_page()
    {
        $this->get(RouteServiceProvider::HOME)
            ->assertViewIs('user.home')
            ->assertViewHas(['latestBlogs', 'oldestBlogs', 'categories', 'featured', 'postOfCategory', 'tags'])
            ->assertOk();

        $this->assertGuest();
    }

    /** @test */
    public function user_can_see_blog_archive()
    {
        $this->get("/blog")
            ->assertViewIs('user.blog')
            ->assertViewHas(['latestBlogs', 'blogs'])
            ->assertOk();
    }

    /** @test */
    public function user_can_see_categories_view()
    {
        $this->get('/category')
            ->assertViewIs('user.categories')
            ->assertViewHas(['latestBlogs', 'tags', 'categories'])
            ->assertOk();
    }

    /** @test */
    public function user_can_see_category_archive()
    {
        $this->withoutExceptionHandling();
        $category = Category::factory()->create(['name' => "bee"]);
        $this->get("/category/{$category->name}")
            ->assertViewIs('user.category')
            ->assertViewHas(['latestBlogs', 'tags', 'category'])
            ->assertOk();
    }

    /** @test */
    public function nested_category_can_be_added_to_the_route()
    {
        $category = Category::factory()->create(['name' => "bee"]);
        $children = Category::factory()->create(['name' => "boo", 'parent_id' => $category->id, "depth" => 1]);

        $this->get("/category/{$category->name}/{$children->name}")
            ->assertOk();
    }

    /** @test */
    public function user_can_see_blog_single_view()
    {
        $this->withoutExceptionHandling();
        $blog = Blog::factory()->create();
        $category = Category::factory()->create(['name' => "bee"]);
        $children = Category::factory()->create(['name' => "boo", 'parent_id' => $category->id, "depth" => 1]);

        $this->get("/category/{$category->name}/{$children->name}/{$blog->slug}")
            ->assertViewIs('user.single')
            ->assertViewHas('blog')
            ->assertOk();
    }
}
