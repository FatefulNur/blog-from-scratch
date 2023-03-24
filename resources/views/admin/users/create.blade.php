@extends('layouts.admin.admin')
@section('title', 'Add New User')

@section('heading', 'Add User')

@section('body')

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Add a new user
                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-7">
                            <form role="form" action="{{ route('admin.users.store') }}" method="POST">
                                @include('partials.admin.errors', [
                                    'errors' => $errors,
                                ])
                                @csrf
                                <div class="form-group @error('name') has-error @enderror">
                                    <label class="control-label" for="name">Name</label>
                                    <input type="text" name="name" class="form-control" id="name"
                                        value="{{ old('name') }}">
                                </div>
                                <div class="form-group @error('email') has-error @enderror">
                                    <label class="control-label" for="email">Email</label>
                                    <input type="text" name="email" class="form-control" id="email"
                                        value="{{ old('email') }}">
                                </div>
                                <div class="form-group @error('password') has-error @enderror">
                                    <label class="control-label" for="password">Password</label>
                                    <input type="password" name="password" class="form-control" id="password">
                                </div>
                                <div class="form-group @error('password_confirmation') has-error @enderror">
                                    <label class="control-label" for="password_confirmation">Retype Password</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                        id="password_confirmation">
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="role">Change Role</label>
                                    <select class="form-control" name="role">
                                        <option value="1" @if (old('role') == 1) {{ 'selected' }} @endif>
                                            Admin</option>
                                        <option value="2" @if (old('role') == 2) {{ 'selected' }} @endif>
                                            User</option>

                                    </select>

                                </div>
                                <button type="submit" class="btn btn-primary">Add User</button>
                                <button type="reset" class="btn btn-success">Reset User</button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
