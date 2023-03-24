<?php

namespace Tests\Feature\Admin;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_see_create_tag_page()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/tags/create')
            ->assertViewIs('admin.tags.create')
            ->assertViewHas('tags')
            ->assertOk();
    }

    /** @test */
    public function admin_can_create_a_tag()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/tags/store', [
                'name' => 'lambohrgini',
            ])
            ->assertValid()
            ->assertRedirectToRoute('admin.tags.create')
            ->assertSessionHas('action');

        $this->assertDatabaseCount('tags', 1);
    }

    /** @test */
    public function admin_can_delete_a_tag()
    {
        $tag = Tag::factory(3)->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->delete('/admin/tags/' . $tag[0]->name . '/delete')
            ->assertValid()
            ->assertRedirectToRoute('admin.tags.create')
            ->assertSessionHas('action');

        $this->assertDatabaseCount('tags', 2);
    }

    /** @test */
    public function admin_can_see_edit_tag_page()
    {
        $tag = Tag::factory()->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/tags/'. $tag->name .'/edit')
            ->assertViewIs('admin.tags.edit')
            ->assertOk();
    }

    /** @test */
    public function admin_can_update_a_tag()
    {
        $tag = Tag::factory(2)->create();

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/tags/' . $tag[0]->name . '/update', [
                'name' => 'Updated'
            ])
            ->assertValid()
            ->assertRedirectToRoute('admin.tags.create')
            ->assertSessionHas('action');

        $this->assertEquals('Updated', $tag[0]->fresh()->name);
    }
}
