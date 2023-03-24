<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_see_dashboard()
    {
        $this->actingAs(User::factory()->create(['role' => 1]))
            ->get(RouteServiceProvider::ADMIN)
            ->assertOk()
            ->assertViewIs('admin.dashboard');
    }

    /** @test */
    public function admin_cannot_see_dashboard_when_logged_out()
    {
        $this->get(RouteServiceProvider::ADMIN)
            ->assertRedirect('/admin/login');
    }

    /** @test */
    public function user_cannot_visit_admin()
    {
        $this->actingAs(User::factory()->create(['role' => 2]))
            ->get(RouteServiceProvider::ADMIN)
            ->assertForbidden();
    }

    /** @test */
    public function admin_has_a_users_page()
    {
        $this->actingAs(User::factory()->create(['role' => 1]))
            ->get('/admin/users')
            ->assertViewIs('admin.users.index')
            ->assertViewHas('users', User::all());
    }

    /** @test */
    public function admin_can_view_a_user_details()
    {
        $this->actingAs($user = User::factory()->create(['role' => 1]))
            ->get('/admin/users/' . $user->id)
            ->assertViewIs('admin.users.show')
            ->assertViewHas('user', $user);
    }

    /** @test */
    public function admin_can_make_a_user_to_admin()
    {
        $user = User::factory()->create(['role' => 2]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->patch('/admin/users/' . $user->id . '/make-admin')
            ->assertRedirect(URL::previous())
            ->assertHeader('statusText');

        $this->assertEquals(1, $user->first()->role);
    }

    /** @test */
    public function admin_can_make_an_admin_to_user()
    {
        $user = User::factory()->create(['role' => 2]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->patch('/admin/users/' . $user->id . '/make-user')
            ->assertRedirect(URL::previous())
            ->assertHeader('statusText');

        $this->assertEquals(2, $user->first()->role);
    }

    /** @test */
    public function admin_can_delete_a_user()
    {
        $user = User::factory()->create(['role' => 2]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->from('/admin/users')
            ->delete('/admin/users/' . $user->id . '/destroy')
            ->assertRedirect(URL::previous())
            ->assertSessionHas('action');

        $this->assertNull($user->find(1));
    }

    /** @test */
    public function admin_can_edit_a_user()
    {
        $user = User::factory()->create(['role' => 2]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/users/' . $user->id . '/edit')
            ->assertViewHas('user')
            ->assertViewIs('admin.users.edit');
    }

    /** @test */
    public function admin_can_update_a_user()
    {
        $user = User::factory()->create(['role' => 2]);

        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->from('/admin/users')
            ->put('/admin/users/' . $user->id . '/update', [
                'name' => 'Nurunnabi',
                'email' => 'nurun@test.com',
                'password' => 'admin111',
                'password_confirmation' => 'admin111',
                'role' => 1
            ])->assertValid()
            ->assertRedirect('/admin/users')
            ->assertSessionHas('action');

        $this->assertEquals('Nurunnabi', $user->first()->name);
        $this->assertEquals('nurun@test.com', $user->first()->email);
        $this->assertEquals(Hash::check('admin111', $user->first()->password), $user->first()->password);
        $this->assertEquals(1, $user->first()->role);
    }

    /** @test */
    public function admin_can_see_user_create_page()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->get('/admin/users/create')
            ->assertViewIs('admin.users.create');
    }

    /** @test */
    public function admin_can_create_a_user()
    {
        $this->actingAs($admin = User::factory()->create(['role' => 1]))
            ->post('/admin/users/store', [
                'name' => 'Nurun',
                'email' => 'nurun@test.com',
                'password' => 'nurun123',
                'password_confirmation' => 'nurun123',
                'role' => 2
            ])->assertValid()
            ->assertRedirect('/admin/users')
            ->assertSessionHas('action');

        $this->assertCount(2, User::all());
        $this->assertEquals('Nurun', User::find(2)->name);
        $this->assertEquals('nurun@test.com', User::find(2)->email);
        $this->assertEquals(Hash::check('nurun123', User::find(2)->password), User::find(2)->password);
        $this->assertEquals(2, User::find(2)->role);
    }
}
