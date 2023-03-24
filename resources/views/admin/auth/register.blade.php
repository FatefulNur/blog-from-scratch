@extends('layouts.auth.form')

@section('title', 'Admin Registration')

@section('heading', 'Admin Registration')

@section('body')
    <form id="login-form" method="POST" action="{{ route('admin.register') }}">
        @csrf
        <p>
            <input type="text" id="name" name="name" placeholder="User Name" value="{{ old('name') }}"><i
                class="validation"><span></span><span></span></i>
                @error('name')
                    <small>{{ $message }}</small>
                @enderror
        </p>
        <p>
            <input type="text" id="email" name="email" placeholder="User Email" value="{{ old('email') }}"><i
                class="validation"><span></span><span></span></i>
                @error('email')
                    <small>{{ $message }}</small>
                @enderror
        </p>
        <p>
            <input type="password" id="password" name="password" placeholder="Password"><i
                class="validation"><span></span><span></span></i>
                @error('password')
                    <small>{{ $message }}</small>
                @enderror
        </p>
        <p>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password"><i
                class="validation"><span></span><span></span></i>
        </p>
        <div>
            <label for="remember">Remember me</label>
            <input type="checkbox" id="remember" name="remember">
        </div>
        <p>
            <input type="submit" id="login" value="Register">
        </p>
    </form>
    <div id="create-account-wrap">
        <p>Has an account? <a href="{{ route('admin.login.index') }}">Login Now</a>
        <p>
    </div>
    <!--create-account-wrap-->
@endsection
