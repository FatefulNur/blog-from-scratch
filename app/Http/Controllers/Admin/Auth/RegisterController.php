<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function register()
    {
        return view('admin.auth.register');
    }

    public function create(Request $request)
    {
        $this->validatedRequests($request);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 1
        ]);

        Auth::login($user, $request->has('remember'));

        return redirect(RouteServiceProvider::ADMIN);
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
