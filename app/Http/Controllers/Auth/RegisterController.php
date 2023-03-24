<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['guest', 'membership']);
    }

    public function register()
    {
        return view('auth.register');
    }

    public function create(Request $request)
    {
        $this->validatedRequests($request);

        $default_role = Setting::first()->default_role;

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $default_role
        ]);

        Auth::login($user, $request->has('remember'));

        return redirect(RouteServiceProvider::HOME);
    }

    private function validatedRequests(Request $request)
    {
        return $request->validate([
            'name' => 'required|alpha|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|max:24|confirmed'
        ]);
    }
}
