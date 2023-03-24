<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_register_form()
    {
        $this->get('/register')
            ->assertSuccessful()
            ->assertViewIs('auth.register');
    }

    /** @test */
    public function admin_can_view_register_form()
    {
        $this->get('/admin/register')
            ->assertOk()
            ->assertViewIs('admin.auth.register');
    }

    /** @test */
    public function user_can_be_created()
    {
        $this->post('/register', $this->data())
            ->assertRedirect(RouteServiceProvider::HOME);

        $this->assertCount(1, User::all());
        $this->assertEquals('Nurun', User::first()->name);
        $this->assertEquals('nurun@test.com', User::first()->email);
        $this->assertEquals(Hash::check('nurun123', User::first()->password), User::first()->password);
        $this->assertEquals(2, User::first()->role);
        $this->assertAuthenticatedAs(User::firstWhere('role', 2));
    }

    /** @test */
    public function admin_can_be_created()
    {
        $this->post('/admin/register', $this->data(['role' => 1]))
            ->assertRedirect(RouteServiceProvider::ADMIN);

        $this->assertCount(1, User::all());
        $this->assertEquals('Nurun', User::first()->name);
        $this->assertEquals('nurun@test.com', User::first()->email);
        $this->assertEquals(Hash::check('nurun123', User::first()->password), User::first()->password);
        $this->assertEquals(1, User::first()->role);
        $this->assertAuthenticatedAs(User::firstWhere('role', 1));
    }

    /** @test */
    public function user_cannot_use_same_email_for_register()
    {
        $this->post('/register', $this->data())
            ->assertRedirect(RouteServiceProvider::HOME);

        Auth::logout();

        $this->from('/register')->post('/register', $this->data())
            ->assertRedirect('/register')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /** @test */
    public function admin_cannot_use_same_email_for_register()
    {
        $this->post('/admin/register', $this->data(['role' => 1]))
            ->assertRedirect(RouteServiceProvider::ADMIN);

        Auth::logout();

        $this->from('/admin/register')->post('/admin/register', $this->data(['role' => 1]))
            ->assertRedirect('/admin/register')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /** @test */
    public function user_cannot_see_register_when_authenticated()
    {
        $this->actingAs(User::factory()->create(['role' => 2]))
            ->get('/register')
            ->assertRedirect(RouteServiceProvider::HOME);
    }

    /** @test */
    public function admin_cannot_see_register_when_authenticated()
    {
        $this->actingAs(User::factory()->create(['role' => 1]))
            ->get('/admin/register')
            ->assertRedirect(RouteServiceProvider::ADMIN);
    }

    /** @test */
    public function user_can_be_remembered_when_registered()
    {
        $this->post('/register', $this->data(['role' => 2, 'remember' => 'on']))
            ->assertRedirect(RouteServiceProvider::HOME)
            ->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
                User::first()->id,
                User::first()->getRememberToken(),
                User::first()->password,
            ]));

        $this->assertAuthenticatedAs(User::firstWhere('role', 2));
    }

    /** @test */
    public function admin_can_be_remembered_when_registered()
    {
        $this->post('/admin/register', $this->data(['role' => 1, 'remember' => 'on']))
            ->assertRedirect(RouteServiceProvider::ADMIN)
            ->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
                User::first()->id,
                User::first()->getRememberToken(),
                User::first()->password,
            ]));

        $this->assertAuthenticatedAs(User::firstWhere('role', 1));
    }

    private function data(array $mergedWith = [])
    {
        return array_merge([
            'name' => 'Nurun',
            'email' => 'nurun@test.com',
            'password' => 'nurun123',
            'password_confirmation' => 'nurun123',
            'role' => 2
        ], $mergedWith);
    }
}
