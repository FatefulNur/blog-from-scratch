<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_a_login_form()
    {
        $this->get('/login')
            ->assertSuccessful()
            ->assertViewIs('auth.login');
    }

    /** @test */
    public function admin_can_view_a_login_form()
    {
        $this->get('/admin/login')
            ->assertSuccessful()
            ->assertViewIs('admin.auth.login');
    }

    /** @test */
    public function user_cannot_view_login_form_when_authenticated()
    {
        $this->actingAs(User::factory()->create($this->data()))
            ->get('/login')
            ->assertRedirect(RouteServiceProvider::HOME);
    }

    /** @test */
    public function admin_cannot_view_login_form_when_authenticated()
    {
        $this->actingAs(User::factory()->create(['role' => 1]))
            ->get('/admin/login')
            ->assertRedirect(RouteServiceProvider::ADMIN);
    }

    /** @test */
    public function user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create($this->data());

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'nur123',
            'role' => $user->role
        ])
            ->assertRedirect(RouteServiceProvider::HOME);

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function admin_can_login_with_correct_credentials()
    {
        $user = User::factory()->create($this->data(['role' => 1]));

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'nur123',
            'role' => $user->role
        ])
            ->assertRedirect(RouteServiceProvider::ADMIN);

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_incorrect_credentials()
    {
        $user = User::factory()->create($this->data());

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'nuf123',
            'role' => 2
        ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('error');

        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    /** @test */
    public function admin_cannot_login_with_incorrect_credentials()
    {
        $user = User::factory()->create($this->data(['role' => 1]));

        $this->from('/admin/login')->post('/admin/login', [
            'email' => $user->email,
            'password' => 'nur123a',
            'role' => $user->role
        ])
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors('error');

        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    /** @test */
    public function user_cannot_login_with_admin_form()
    {
        $user = User::factory()->create($this->data());

        $this->from('/admin/login')->post('/admin/login', [
            'email' => $user->email,
            'password' => 'nur123',
            'role' => $user->role
        ])->assertRedirect('/login');
        $this->assertGuest();
    }

    /** @test */
    public function admin_can_login_with_user_form()
    {
        $user = User::factory()->create($this->data(['role' => 1]));

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'nur123',
            'role' => $user->role
        ])->assertRedirect(RouteServiceProvider::HOME);
    }

    /** @test */
    public function admin_can_login_with_admin_form()
    {
        $user = User::factory()->create($this->data(['role' => 1]));

        $this->from('/admin/login')->post('/admin/login', [
            'email' => $user->email,
            'password' => 'nur123',
            'role' => $user->role
        ])->assertRedirect(RouteServiceProvider::ADMIN);
    }


    /** @test */
    public function user_can_remember_his_login()
    {
        $user = User::factory()->create($this->data(['id' => random_int(1, 100)]));

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'nur123',
            'remember' => 'on',
        ])
            ->assertRedirect(RouteServiceProvider::HOME)
            ->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
                $user->id,
                $user->getRememberToken(),
                $user->password,
            ]));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function admin_can_remember_his_login()
    {
        $user = User::factory()->create($this->data(['role' => 1, 'id' => random_int(1, 100)]));

        $this->from('/admin/login')->post('/admin/login', [
            'email' => $user->email,
            'password' => 'nur123',
            'remember' => 'on',
            'role' => 1
        ])
            ->assertRedirect(RouteServiceProvider::ADMIN)
            ->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
                $user->id,
                $user->getRememberToken(),
                $user->password,
            ]));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_can_logout()
    {
        $this->actingAs(User::factory()->create($this->data()))
            ->from(RouteServiceProvider::HOME)
            ->get('/logout')
            ->assertRedirect(RouteServiceProvider::HOME);
        $this->assertGuest();
    }

    /** @test */
    public function admin_can_logout()
    {
        $this->actingAs(User::factory()->create(['role' => 1]))
            ->from(RouteServiceProvider::ADMIN)
            ->get('/admin/logout')
            ->assertRedirect(RouteServiceProvider::HOME);
        $this->assertGuest();
    }

    /** @test */
    public function user_cannot_access_admin_logout()
    {
        $res = $this->actingAs(User::factory()->create($this->data()))
            ->get('/admin/logout')
            ->assertForbidden();
    }

    /** @test */
    public function admin_cannot_access_user_logout()
    {
        $res = $this->actingAs(User::factory()->create(['role' => 1]))
            ->get('/logout')
            ->assertForbidden();
    }

    private function data(array $mergedWith = [])
    {
        return array_merge([
            'role' => 2,
            'password' => Hash::make('nur123')
        ], $mergedWith);
    }
}
