<?php

namespace Tests\Feature\Admin;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function admin_has_blog_list()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/blogs')
            ->assertViewIs('admin.blogs.index')
            ->assertViewHas(['blogs', 'thumbnails', 'trashes']);
    }

    /** @test */
    public function admin_has_create_blog_page()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/blogs/create')
            ->assertViewIs('admin.blogs.create')
            ->assertOk();
    }

    /** @test */
    public function admin_can_create_a_blog()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/blogs/store', $this->blogdata($admin))
            ->assertValid()
            ->assertRedirect('/admin/blogs')
            ->assertSessionHas('action')
            ->assertSessionMissing(['image', 'gallery']);

        $this->assertDatabaseCount('blogs', 1);
        $this->assertDatabaseHas('blogs', [
            'title' => $this->blogdata($admin)['title'],
            'slug' => Blog::first()->slug,
            'short_desc' => $this->blogdata($admin)['short_desc'],
            'description' => $this->blogdata($admin)['description'],
            'user_id' => $this->blogdata($admin)['user_id'],
        ]);

        $this->assertTrue(Cache::has('xs-blogs-caches'));
        $this->assertFalse(Cache::has('xs-blog-thumb-caches'));
        $this->assertFalse(Cache::has('xs-blog-gallery-images-caches'));
    }

    /** @test */
    public function admin_can_create_blog_of_any_user()
    {
        $user = User::factory()->has(Blog::factory())->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/blogs/store', $this->blogdata($user))
            ->assertValid()
            ->assertSessionHasNoErrors();
    }

    /** @test */
    public function admin_can_set_blog_category()
    {
        $category = Category::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/blogs/store', $this->blogdata($admin, [
                'parent_id' => $category->id
            ]));

        $this->assertEquals(1, Blog::first()->category_id);
    }

    /** @test */
    public function default_category_will_be_created_if_not_exists()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/blogs/store', $this->blogdata($admin, [
                'parent_id' => null
            ]));

        $this->assertDatabaseCount('categories', 1);
        $this->assertEquals('Uncategorized', Blog::first()->category->name);
    }

    /** @test */
    public function admin_can_comment_of_a_blog()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/blogs/store', $this->blogdata($admin, [
                'can_commented' => 1
            ]));

        $this->assertEquals(true, Blog::first()->can_commented);
    }

    /** @test */
    public function admin_can_create_blog_image()
    {
        $file = UploadedFile::fake()->image('photo.png');

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/blogs/store', $this->blogdata($admin, [
                't_name' => 'some image',
                't_caption' => 'beatiful image',
                't_details' => 'some details',
                'thumbnail' => $file
            ]))
            ->assertValid()
            ->assertRedirect('/admin/blogs')
            ->assertSessionHas(['action', 'image'])
            ->assertSessionMissing('gallery');

        $this->assertDatabaseCount('images', 1);
        $this->assertDatabaseHas('images', [
            'name' => 'some image',
            'caption' => 'beatiful image',
            'details' => 'some details',
            'path' => $file->hashName('/uploads/thumbnail'),
            'imagable_id' => Blog::first()->id,
            'imagable_type' => 'App\Models\Blog'
        ]);

        $this->assertTrue(Cache::has('xs-blogs-caches'));
        $this->assertTrue(Cache::has('xs-blog-thumb-caches'));
        $this->assertFalse(Cache::has('xs-blog-gallery-images-caches'));
        $this->assertFileExists(public_path('uploads/thumbnail/' . $file->hashName()));

        unlink(public_path('uploads/thumbnail/' . $file->hashName()));
    }

    /** @test */
    public function admin_can_create_blog_gallery()
    {
        $files = [
            UploadedFile::fake()->image('photo.png'),
            UploadedFile::fake()->image('photo2.png'),
        ];
        $data = [
            'g_name' => 'some image',
            'g_caption' => 'beatiful image',
            'g_details' => 'some details',
            'galleries' => $files
        ];

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/blogs/store', $this->blogdata($admin, $data))
            ->assertValid()
            ->assertRedirect('/admin/blogs')
            ->assertSessionHas(['action', 'gallery'])
            ->assertSessionMissing('image');

        $this->assertDatabaseCount('galleries', 1);
        $this->assertDatabaseHas('galleries', [
            'user_id' => $admin->id,
            'blog_id' => 1
        ]);
        $this->assertDatabaseCount('images', 2);
        $this->assertDatabaseHas('images', [
            'name' => $data['g_name'],
            'caption' => $data['g_caption'],
            'details' => $data['g_details'],
            'path' => $files[0]->hashName('/uploads/gallery'),
            'imagable_id' => Gallery::first()->id,
            'imagable_type' => 'App\Models\Gallery'
        ]);
        $this->assertDatabaseHas('images', [
            'name' => $data['g_name'],
            'caption' => $data['g_caption'],
            'details' => $data['g_details'],
            'path' => $files[1]->hashName('/uploads/gallery'),
            'imagable_id' => Gallery::find(1)->id,
            'imagable_type' => 'App\Models\Gallery'
        ]);

        $this->assertTrue(Cache::has('xs-blogs-caches'));
        $this->assertTrue(Cache::has('xs-blog-gallery-images-caches'));
        $this->assertFalse(Cache::has('xs-blog-thumb-caches'));
        $this->assertFileExists(public_path('uploads/gallery/' . $files[0]->hashName()));
        $this->assertFileExists(public_path('uploads/gallery/' . $files[1]->hashName()));

        unlink(public_path('uploads/gallery/' . $files[0]->hashName()));
        unlink(public_path('uploads/gallery/' . $files[1]->hashName()));
    }

    /** @test */
    public function admin_can_soft_delete_a_blog()
    {
        $blog = Blog::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->from('/admin/blogs')
            ->delete('/admin/blogs/' . $blog->slug . '/delete')
            ->assertRedirect(URL::previous())
            ->assertSessionHas('action');

        $this->assertNotNull($blog->fresh()->deleted_at);
        $this->assertSoftDeleted($blog);
        $this->assertTrue(Cache::has('xs-blogs-caches'));
        $this->assertTrue(Cache::has("xs-only-trashed-blogs-caches"));
    }

    /** @test */
    public function admin_can_see_trash_page()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/blogs/trash')
            ->assertOk()
            ->assertViewIs('admin.blogs.trash')
            ->assertViewHas('trashes');
    }

    /** @test */
    public function admin_can_forced_a_blog_to_delete()
    {
        $blog = Blog::factory()->create(['deleted_at' => now()]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->from('/admin/blogs/trash')
            ->delete('/admin/blogs/trash/' . $blog->slug . '/delete')
            ->assertRedirect(URL::previous())
            ->assertSessionHas('action');
        $this->assertDatabaseEmpty($blog);
        $this->assertTrue(Cache::has('xs-blogs-caches'));
        $this->assertTrue(Cache::has("xs-only-trashed-blogs-caches"));
    }

    /** @test */
    public function media_is_removed_after_forced_delete_an_item()
    {
        $blog = Blog::factory()->create(['deleted_at' => now()]);
        $image = UploadedFile::fake()->image('photo.png');
        $images = [
            UploadedFile::fake()->image('image.png'),
            UploadedFile::fake()->image('image2.png'),
        ];
        $thumbnail = $blog->image()->create(['path' => 'uploads/thumbnail/' . $image->hashName()]);
        $gallery = $blog->gallery()->create(['user_id' => $blog->user_id]);
        $gallery_images = $blog->gallery->images()->createMany([
            ['path' => 'uploads/gallery/' . $images[0]->hashName()],
            ['path' => 'uploads/gallery/' . $images[1]->hashName()],
        ]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->from('/admin/blogs/trash')
            ->delete('/admin/blogs/trash/' . $blog->slug . '/delete')
            ->assertRedirect(URL::previous())
            ->assertSessionHas(['image', 'gallery']);

        $this->assertDatabaseCount($blog, 0);
        $this->assertDatabaseCount($thumbnail, 0);
        $this->assertDatabaseCount($gallery_images[0], 0);
        $this->assertDatabaseCount($gallery, 0);
        $this->assertFileDoesNotExist(public_path('uploads/thumbnail') . $image->hashName());
        $this->assertFileDoesNotExist(public_path('uploads/gallery') . $images[0]->hashName());
        $this->assertFileDoesNotExist(public_path('uploads/gallery') . $images[1]->hashName());
    }

    /** @test */
    public function admin_can_forced_entire_blog_to_delete()
    {
        $blog = Blog::factory()->create(['deleted_at' => now()]);
        $blog2 = Blog::factory()->create(['deleted_at' => now()]);
        $blog3 = Blog::factory()->create(['deleted_at' => now()]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->from('/admin/blogs/trash')
            ->delete('/admin/blogs/trash/empty')
            ->assertRedirect(URL::previous())
            ->assertSessionHas('action');
        $this->assertDatabaseEmpty($blog);
        $this->assertDatabaseEmpty($blog2);
        $this->assertDatabaseEmpty($blog3);
        $this->assertFalse(Cache::has("xs-only-trashed-blogs-caches"));
    }

    /** @test */
    public function media_is_removed_after_forced_delete_entire_item()
    {
        $blog = Blog::factory()->create(['deleted_at' => now(), 'title' => 'nur']);
        $blog2 = Blog::factory()->create(['deleted_at' => now()]);

        $image = UploadedFile::fake()->image('photo.png');
        $image2 = UploadedFile::fake()->image('photo2.png');

        $images = [
            UploadedFile::fake()->image('image.png'),
            UploadedFile::fake()->image('image2.png'),
        ];
        $images2 = [
            UploadedFile::fake()->image('images.png'),
            UploadedFile::fake()->image('images2.png'),
        ];

        $thumbnail = $blog->image()->create(['path' => 'uploads/thumbnail/' . $image->hashName()]);
        $thumbnail2 = $blog2->image()->create(['path' => 'uploads/thumbnail/' . $image2->hashName()]);

        $gallery = $blog->gallery()->create(['user_id' => $blog->user_id]);
        $gallery2 = $blog2->gallery()->create(['user_id' => $blog2->user_id]);

        $gallery_images = $blog->gallery->images()->createMany([
            ['path' => 'uploads/gallery/' . $images[0]->hashName()],
            ['path' => 'uploads/gallery/' . $images[1]->hashName()],
        ]);
        $gallery_images2 = $blog2->gallery->images()->createMany([
            ['path' => 'uploads/gallery/' . $images2[0]->hashName()],
            ['path' => 'uploads/gallery/' . $images2[1]->hashName()],
        ]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->from('/admin/blogs/trash')
            ->delete('/admin/blogs/trash/empty')
            ->assertRedirect(URL::previous())
            ->assertSessionHas(['image', 'gallery']);

        $this->assertDatabaseCount($blog, 0);
        $this->assertDatabaseCount($blog2, 0);
        $this->assertDatabaseCount($thumbnail, 0);
        $this->assertDatabaseCount($thumbnail2, 0);
        $this->assertDatabaseCount($gallery_images[0], 0);
        $this->assertDatabaseCount($gallery_images2[0], 0);
        $this->assertDatabaseCount($gallery, 0);
        $this->assertDatabaseCount($gallery2, 0);
        $this->assertFileDoesNotExist(public_path('uploads/thumbnail') . $image->hashName());
        $this->assertFileDoesNotExist(public_path('uploads/thumbnail') . $image2->hashName());
        $this->assertFileDoesNotExist(public_path('uploads/gallery') . $images[0]->hashName());
        $this->assertFileDoesNotExist(public_path('uploads/gallery') . $images[1]->hashName());
        $this->assertFileDoesNotExist(public_path('uploads/gallery') . $images2[0]->hashName());
        $this->assertFileDoesNotExist(public_path('uploads/gallery') . $images2[1]->hashName());
    }

    /** @test */
    public function admin_can_restore_a_blog()
    {
        $blog = Blog::factory()->create(['deleted_at' => now()]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->from('/admin/blogs/trash')
            ->patch('/admin/blogs/trash/' . $blog->slug . '/restore')
            ->assertRedirect(URL::previous())
            ->assertSessionHas('action');

        $this->assertNull($blog->fresh()->deleted_at);
        $this->assertNotSoftDeleted($blog);
        $this->assertTrue(Cache::has("xs-only-trashed-blogs-caches"));
    }

    /** @test */
    public function admin_can_restore_entire_blog()
    {
        $blog = Blog::factory()->create(['deleted_at' => now()]);
        $blog2 = Blog::factory()->create(['deleted_at' => now()]);
        $blog3 = Blog::factory()->create(['deleted_at' => now()]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->from('/admin/blogs/trash')
            ->patch('/admin/blogs/trash/restore')
            ->assertRedirect(URL::previous())
            ->assertSessionHas('action');

        $this->assertNull($blog->fresh()->deleted_at);
        $this->assertNull($blog2->fresh()->deleted_at);
        $this->assertNull($blog3->fresh()->deleted_at);
        $this->assertNotSoftDeleted($blog);
        $this->assertNotSoftDeleted($blog2);
        $this->assertNotSoftDeleted($blog3);
    }

    /** @test */
    public function admin_can_see_edit_page()
    {
        $blog = Blog::factory()->create();
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/blogs/' . $blog->slug . '/edit')
            ->assertViewIs('admin.blogs.edit')
            ->assertViewHas(['blog', 'gallery']);
    }

    /** @test */
    public function admin_can_update_a_blog()
    {
        $blog = Blog::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/blogs/' . $blog->slug . '/update', [
                'title' => 'Updated Title',
                'slug' => $blog->slug,
                'short_desc' => 'Updated short description',
                'description' => 'Updated blog description',
                'user_id' => $user->id
            ])
            ->assertValid()
            ->assertRedirect('/admin/blogs')
            ->assertSessionHas('action')
            ->assertSessionMissing(['image', 'gallery']);

        $this->assertDatabaseCount($blog, 1);
        $this->assertDatabaseHas($blog, [
            'title' => $blog->fresh()->title,
            'slug' => $blog->fresh()->slug,
            'short_desc' => $blog->fresh()->short_desc,
            'description' => $blog->fresh()->description,
            'user_id' => $user->id,
        ]);
        $this->assertTrue(Cache::has('xs-blogs-caches'));
        $this->assertTrue(Cache::has('xs-blog-thumb-caches'));
        $this->assertTrue(Cache::has('xs-blog-gallery-images-caches'));
    }

    /** @test */
    public function admin_can_update_can_commented_option()
    {
        $blog = Blog::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/blogs/' . $blog->slug . '/update', [
                'title' => 'Updated Title',
                'slug' => $blog->slug,
                'short_desc' => 'Updated short description',
                'description' => 'Updated blog description',
                'user_id' => $user->id,
                'can_commented' => "off"
            ]);

        $this->assertEquals(true, Blog::first()->can_commented);
    }

    /** @test */
    public function admin_can_update_a_thumbnail()
    {
        $blog = Blog::factory()->create();
        $photo = UploadedFile::fake()->image('photo.png');
        $blog_image = $blog->image()->create(['path' => 'uploads/thumbnail/' . $photo->hashName()]);

        $file = UploadedFile::fake()->image('updated-photo.png');
        $user = User::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/blogs/' . $blog->slug . '/update', [
                'title' => $blog->title,
                'slug' => $blog->slug,
                'short_desc' => $blog->short_desc,
                'description' => $blog->description,
                'user_id' => $user->id,
                't_name' => 'Updated image',
                't_caption' => 'Updated beatiful image',
                't_details' => 'Updated details',
                'thumbnail' => $file
            ])
            ->assertValid()
            ->assertRedirect('/admin/blogs')
            ->assertSessionHas(['action', 'image'])
            ->assertSessionMissing('gallery');

        $this->assertDatabaseCount($blog_image->fresh(), 1);
        $this->assertDatabaseHas($blog_image, [
            'name' => 'Updated image',
            'caption' => 'Updated beatiful image',
            'details' => 'Updated details',
            'path' => $file->hashName('/uploads/thumbnail'),
            'imagable_id' => $blog->fresh()->id,
            'imagable_type' => 'App\Models\Blog'
        ]);

        $this->assertTrue(Cache::has('xs-blogs-caches'));
        $this->assertTrue(Cache::has('xs-blog-thumb-caches'));
        $this->assertTrue(Cache::has('xs-blog-gallery-images-caches'));
        $this->assertFileExists(public_path('uploads/thumbnail/' . $file->hashName()));
        $this->assertFileDoesNotExist(public_path('uploads/thumbnail/' . $blog_image->path));

        unlink(public_path('uploads/thumbnail/' . $file->hashName()));
    }

    /** @test */
    public function admin_can_update_a_gallery()
    {
        $blog = Blog::factory()->create();
        $user = User::factory()->create();
        $images = [
            UploadedFile::fake()->image('photo.png'),
            UploadedFile::fake()->image('photo3.png')
        ];

        $gallery = $blog->gallery()->create(['user_id' => $user->id]);
        $gallery_images = $gallery->images()->createMany([
            ['path' => 'uploads/gallery/' . $images[0]->hashName(), 'name' => 'poop'],
            ['path' => 'uploads/gallery/' . $images[1]->hashName(), 'name' => 'popo'],
        ]);
        $uploads = [
            UploadedFile::fake()->image('updated-photo.png'),
            UploadedFile::fake()->image('updated-photo2.png'),
            UploadedFile::fake()->image('updated-photo3.png'),
        ];

        $ids = [
            $gallery_images[0]->id,
            $gallery_images[1]->id,
        ];

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/blogs/' . $blog->slug . '/update', [
                'title' => $blog->title,
                'slug' => $blog->slug,
                'short_desc' => $blog->short_desc,
                'description' => $blog->description,
                'user_id' => $user->id,
                'g_name' => 'Updated Gallery',
                'g_caption' => 'Updated beautiful Gallery',
                'g_details' => 'Updated Gallery details',
                'galleries' => $uploads,
                'ids' => $ids
            ])
            ->assertValid()
            ->assertRedirect('/admin/blogs')
            ->assertSessionHas(['action', 'gallery'])
            ->assertSessionMissing('image');

        $this->assertDatabaseCount($gallery, 1);
        $this->assertDatabaseCount('images', count($uploads));

        $this->assertDatabaseHas($gallery_images[0], [
            'name' => 'Updated Gallery',
            'caption' => 'Updated beautiful Gallery',
            'details' => 'Updated Gallery details',
            'path' => $uploads[0]->hashName('/uploads/gallery'),
            'imagable_id' => $gallery->id,
            'imagable_type' => 'App\Models\Gallery'
        ]);

        $this->assertDatabaseHas($gallery_images[1], [
            'name' => 'Updated Gallery',
            'caption' => 'Updated beautiful Gallery',
            'details' => 'Updated Gallery details',
            'path' => $uploads[1]->hashName('/uploads/gallery'),
            'imagable_id' => $gallery->id,
            'imagable_type' => 'App\Models\Gallery'
        ]);

        $this->assertTrue(Cache::has('xs-blogs-caches'));
        $this->assertTrue(Cache::has('xs-blog-thumb-caches'));
        $this->assertTrue(Cache::has('xs-blog-gallery-images-caches'));
        $this->assertFileExists(public_path('uploads/gallery/' . $uploads[0]->hashName()));
        $this->assertFileExists(public_path('uploads/gallery/' . $uploads[1]->hashName()));
        $this->assertFileDoesNotExist(public_path('uploads/gallery/' . $images[0]->hashName()));
        $this->assertFileDoesNotExist(public_path('uploads/gallery/' . $images[1]->hashName()));

        unlink(public_path('uploads/gallery/' . $uploads[0]->hashName()));
        unlink(public_path('uploads/gallery/' . $uploads[1]->hashName()));
        unlink(public_path('uploads/gallery/' . $uploads[2]->hashName()));
    }

    /** @test */
    public function admin_can_update_category()
    {
        $category = Category::factory()->create();
        $category2 = Category::factory()->create([
            'parent_id' => $category->id
        ]);
        $blog = Blog::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/blogs/' . $blog->slug . '/update', [
                'title' => $blog->title,
                'slug' => $blog->slug,
                'description' => $blog->description,
                'user_id' => 1,
                'parent_id' => $category2->id
            ]);

        $this->assertEquals(2, Blog::first()->category_id);
    }

    /** @test */
    public function admin_can_delete_a_gallery()
    {
        $blog = Blog::factory()->create();
        $user = User::factory()->create();
        $images = [
            UploadedFile::fake()->image('photo.png'),
            UploadedFile::fake()->image('photo2.png'),
        ];

        $gallery = $blog->gallery()->create(['user_id' => $user->id]);
        $gallery_images = $gallery->images()->createMany([
            ['path' => 'uploads/gallery/' . $images[0]->hashName(), 'name' => 'poop'],
            ['path' => 'uploads/gallery/' . $images[1]->hashName(), 'name' => 'popo'],
        ]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->from('/admin/blogs/' . $blog->slug . '/update')
            ->get('/admin/blogs/' . $blog->slug . '/remove-gallery')
            ->assertRedirect(URL::previous())
            ->assertSessionHas('success');

        $this->assertDatabaseEmpty($gallery_images[0]);
        $this->assertDatabaseEmpty($gallery);
    }

    /** @test */
    public function admin_can_remove_thumbnail()
    {
        $blog = Blog::factory()->create();
        $photo = UploadedFile::fake()->image('photo.png');
        $blog_image = $blog->image()->create(['path' => 'uploads/thumbnail/' . $photo->hashName()]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->from('/admin/blogs/' . $blog->slug . '/update')
            ->get('/admin/blogs/' . $blog->slug . '/remove-thumbnail')
            ->assertRedirect(URL::previous())
            ->assertSessionHas('success');
        $this->assertDatabaseEmpty($blog_image);
    }

    /** @test */
    public function admin_can_set_a_featured_post()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/blogs/store', $this->blogdata($admin, [
                'featured' => 'on'
            ]));
        $this->assertEquals(true, Blog::first()->featured);
    }

    /** @test */
    public function admin_can_update_featured_post()
    {
        $blog = Blog::factory()->create(['featured' => false]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/blogs/' . $blog->slug . '/update', $this->blogdata($admin, [
                'slug' => $blog->slug,
                'featured' => "on"
            ]));
        $this->assertEquals(true, $blog->fresh()->featured);
    }

    private function blogdata($user, $mergedWith = [])
    {
        return array_merge([
            'title' => 'This is Blog Title',
            'short_desc' => 'This is blog short description',
            'description' => 'Hi. I\'m admin. This is blog long description. Thanks for visiting our application.',
            'user_id' => $user->id,
            'category_id' => null
        ], $mergedWith);
    }
}
