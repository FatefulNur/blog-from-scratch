<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_see_a_general_page()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/settings/general')
            ->assertViewIs('admin.settings.general')
            ->assertViewHas('generals')
            ->assertOk();
    }

    /** @test */
    public function admin_can_update_generals()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/settings/general', [
                'site_name' => 'My Blogs',
                'tagline' => 'Hi this is blog site',
                'membership' => true,
                'default_role' => 1
            ])
            ->assertRedirect(URL::previous())
            ->assertSessionHas('action');

        $this->assertDatabaseHas('settings', [
            'site_name' => 'My Blogs',
            'tagline' => 'Hi this is blog site',
            'membership' => true,
            'default_role' => 1
        ]);
    }

    /** @test */
    public function admin_can_see_comment_page()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/settings/comment')
            ->assertViewIs('admin.settings.comment')
            ->assertViewHas('comment')
            ->assertOk();
    }

    /** @test */
    public function admin_can_update_comment_page()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->put('/admin/settings/comment', [
                'allow_comment' => 'on',
                'nested_comment' => 'on',
                'max_depth_comment' => 6,
                'comment_permission' => 'on',
            ])
            ->assertRedirect(URL::previous())
            ->assertSessionHas('action');

        $this->assertDatabaseHas('settings', [
            'allow_comment' => 1,
            'nested_comment' => 1,
            'max_depth_comment' => 6,
            'comment_permission' => 1,
        ]);
    }
}
