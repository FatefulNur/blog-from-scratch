@extends('layouts.admin.admin')
@section('title', 'Edit User')

@section('heading', 'Edit User')

@section('body')

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Edit This User
                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-6">
                            <form role="form" action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                @include('partials.admin.errors', ['errors' => $errors])
                                @csrf
                                @method('PUT')
                                <div class="form-group @error('name') has-error @enderror">
                                    <label class="control-label" for="name">Name</label>
                                    <input type="text" name="name" class="form-control" id="name"
                                        value="{{ old('name', $user->name) }}">
                                </div>
                                <div class="form-group @error('email') has-error @enderror">
                                    <label class="control-label" for="email">Email</label>
                                    <input type="text" name="email" class="form-control" id="email"
                                        value="{{ old('email', $user->email) }}">
                                </div>
                                <div class="form-group @error('password') has-error @enderror">
                                    <label class="control-label" for="password">Password</label>
                                    <input type="password" name="password" class="form-control" id="password">
                                    <p class="text-muted">Leave blank to have default password</p>
                                </div>
                                <div class="form-group @error('password_confirmation') has-error @enderror">
                                    <label class="control-label" for="password_confirmation">Retype Password</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        id="password_confirmation">
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="role">Change Role</label>
                                    @unless(auth()->user()->isOwner($user->id))
                                        <select class="form-control" name="role">
                                            <option value="1" @if (old('role', $user->isAdmin()) == 1) {{ 'selected' }} @endif>
                                                Admin</option>
                                            <option value="2" @if (old('role', $user->isNotAdmin()) == 2) {{ 'selected' }} @endif>
                                                User</option>

                                        </select>
                                    @endunless

                                </div>
                                <button type="submit" class="btn btn-primary">Update User</button>
                                <button type="reset" class="btn btn-success">Reset User</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            @include('partials.admin.usercard', ['user' => $user, 'url' => route('admin.users.index')])
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
