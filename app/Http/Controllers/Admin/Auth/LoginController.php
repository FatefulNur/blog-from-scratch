<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login()
    {
        return view('admin.auth.login');
    }

    public function authenticate(Request $request)
    {
        $user = User::firstWhere('email', $request->email);

        if (optional($user)->isNotAdmin()) {
            return redirect('/login');
        }

        $this->validatedRequests($request);
        // dd($request->role);

        if (Auth::attempt($request->only(['email', 'password', 'role' => $request->role]), $request->has('remember'))) {

            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::ADMIN);
        }

        return back()->withErrors([
            'error' => 'Credentials does not match with correct request'
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Gate::authorize('admin-only', auth()->user());

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(RouteServiceProvider::HOME);
    }

    private function validatedRequests(Request $request)
    {
        return $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:24'
        ]);
    }
}
