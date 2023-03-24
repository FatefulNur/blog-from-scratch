<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:admin-only, \App\Models\User']);
    }

    public function index()
    {
        $users = User::orderByDesc('id')->get(['id', 'name', 'email', 'role']);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|alpha|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6|max:24|confirmed'
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role')
        ]);

        if(! empty($user)) {
            return redirect('/admin/users')->with('action', 'User Created');
        }
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function makeAdmin(User $user)
    {
        $user->update(['role' => 1]);

        return back()->withHeaders(['statusText' => 'Successful']);
    }

    public function makeUser(User $user)
    {
        $user->update(['role' => 2]);

        return back()->withHeaders(['statusText' => 'Successful']);
    }

    public function delete(User $user)
    {
        $user->delete();

        return back()->with('action', 'User deleted');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->validateUpdateRequests($request, $user);

        $data = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role')
        ];

        if($user->update($data)) {
            return back()->with('action', 'User updated');
        }
    }

    private function validateUpdateRequests(Request $request, User $user)
    {
        return $request->validate([
            "name" => "required|alpha|min:3|max:55",
            "email" => "required|email|max:255|unique:users,email,{$user->id}",
            "password" => "confirmed|min:6|max:24|nullable"
        ]);
    }
}
