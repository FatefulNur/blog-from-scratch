<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $this->validatedRequests($request);

        if (Auth::attempt($request->only(['email', 'password', 'role' => 2]), $request->has('remember'))) {

            $request->session()->regenerate();

            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return back()->withErrors([
            'error' => 'Credentials does not match with correct request'
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Gate::authorize('user-only', auth()->user());

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
