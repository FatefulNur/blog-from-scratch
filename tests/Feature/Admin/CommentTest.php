<?php

namespace Tests\Feature\Admin;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_see_a_comment_page()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/comments/index')
            ->assertViewIs('admin.comments.index')
            ->assertViewHas('comments')
            ->assertOk();
    }

    /** @test */
    public function admin_can_create_a_comment()
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/comments/store', [
                'user_id' => $user->id,
                'blog_id' => $blog->id,
                'parent_id' => null,
                'body' => 'some happening hello',
                'status' => 'published',
            ])
            ->assertValid()
            ->assertRedirectToRoute('admin.comments.index')
            ->assertSessionHas('action');

        $this->assertDatabaseCount('comments', 1);
        $this->assertDatabaseHas('comments', [
            'user_id' => 1,
            'blog_id' => 1,
            'parent_id' => null,
            'body' => 'some happening hello',
            'status' => 'published',
        ]);
    }

    /** @test */
    public function admin_can_upload_a_photo_in_comment()
    {
        $user = User::factory()->create();
        $blog = Blog::factory()->create();
        $upload = UploadedFile::fake()->image('photo.png');

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/comments/store', [
                'user_id' => $user->id,
                'blog_id' => $blog->id,
                'parent_id' => null,
                'body' => 'some happening hello',
                'status' => 'published',
                'photo' => $upload
            ]);

        $this->assertEquals($upload->hashName('uploads/comment'), Image::first()->path);

        unlink(public_path($upload->hashName('uploads/comment')));
    }

    /** @test */
    public function admin_can_delete_a_comment()
    {
        $parent = Comment::factory()->create();
        $comment = Comment::factory()->create(['parent_id' => $parent->id]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->delete('/admin/comments/' . $parent->id . '/delete')
            ->assertRedirectToRoute('admin.comments.index')
            ->assertSessionHas('action');

        $this->assertDatabaseEmpty('comments');
    }

    /** @test */
    public function image_will_delete_when_comment_deleted()
    {
        $parent = Comment::factory()->create();
        $photo = UploadedFile::fake()->image('photo.png');
        $image = $parent->image()->create(['path' => $photo->hashName('uploads/comment')]);

        $upload = UploadedFile::fake()->image('upload.png');

        $user = User::factory()->create();
        $blog = Blog::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->delete('/admin/comments/' . $parent->id . '/delete');
        $this->assertDatabaseEmpty('images');
        $this->assertDatabaseEmpty('comments');
    }

    /** @test */
    public function admin_can_see_edit_page()
    {
        $comment = Comment::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
        ->get('/admin/comments/'. $comment->id .'/edit')
        ->assertViewIs('admin.comments.edit')
        ->assertViewHas('comment')
        ->assertOk();
    }

    /** @test */
    public function admin_can_update_a_comment()
    {
        $parent = Comment::factory()->create();
        $child = Comment::factory()->create(['parent_id' => $parent->id, 'depth' => 1]);
        $child2 = Comment::factory()->create(['parent_id' => $child->id, 'depth' => 2]);

        $user = User::factory()->create();
        $blog = Blog::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/comments/' . $child2->id . '/update', [
                "user_id" => $user->id,
                "blog_id" => $blog->id,
                "body" => "updated body is here",
                "status" => "approved"
            ])
            ->assertValid()
            ->assertRedirectToRoute('admin.comments.index')
            ->assertSessionHas('action');

        $this->assertDatabaseHas($child2->fresh(), [
            "user_id" => $user->id,
            "blog_id" => $blog->id,
            "body" => "updated body is here",
            "status" => "approved"
        ]);
    }

    /** @test */
    public function admin_can_update_a_photo_in_comment()
    {
        $parent = Comment::factory()->create();
        $child = Comment::factory()->create(['parent_id' => $parent->id, 'depth' => 1]);
        $photo = UploadedFile::fake()->image('photo.png');
        $image = $parent->image()->create(['path' => $photo->hashName('uploads/comment')]);

        $upload = UploadedFile::fake()->image('upload.png');

        $user = User::factory()->create();
        $blog = Blog::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/comments/' . $parent->id . '/update', [
                "user_id" => $user->id,
                "blog_id" => $blog->id,
                "body" => "updated body is here",
                "status" => "approved",
                "photo" => $upload
            ]);

        $this->assertEquals($upload->hashName('uploads/comment'), $image->fresh()->path);
        unlink(public_path($upload->hashName('uploads/comment')));
    }
}
