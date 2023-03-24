<?php

namespace Tests\Feature\Admin;

use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_see_a_media_page()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/media/index')
            ->assertViewIs('admin.media.index')
            ->assertViewHas('images')
            ->assertOk();
    }

    /** @test */
    public function admin_can_see_a_media_details()
    {
        $image = UploadedFile::fake()->image('php.png');
        $data = Image::factory()->create(['path' => $image->hashName()]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/media/' . $data->id . '/show')
            ->assertViewHas('image')
            ->assertViewIs('admin.media.show')
            ->assertOk();
    }

    /** @test */
    public function admin_can_see_an_edit_page()
    {
        $image = UploadedFile::fake()->image('php.png');
        $data = Image::factory()->create(['path' => $image->hashName()]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/media/' . $data->id . '/edit')
            ->assertViewHas('image')
            ->assertViewIs('admin.media.edit')
            ->assertOk();
    }

    /** @test */
    public function admin_can_update_a_media()
    {
        $image = UploadedFile::fake()->image('php.png');
        $data = Image::factory()->create(['path' => $image->hashName()]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->patch('/admin/media/' . $data->id . '/update', [
                'name' => 'Updated name',
                'caption' => 'Updated caption',
                'details' => 'Updated details',
            ])
            ->assertRedirect(URL::previous())
            ->assertSessionHas('action');

        $this->assertDatabaseHas($data->fresh(), [
            'name' => 'Updated name',
            'caption' => 'Updated caption',
            'details' => 'Updated details',
        ]);
    }
}
