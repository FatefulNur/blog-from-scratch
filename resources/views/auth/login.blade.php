@extends('layouts.auth.form')

@section('title', 'User Login')

@section('heading', 'User Login')

    @section('body')
        @include('partials.admin.errors', ['errors' => $errors])

    <form id="login-form" method="POST" action="{{ route('login') }}">
        @csrf
        <p>
            <input type="text" id="email" name="email" placeholder="Email" value="{{ old('email') }}"><i
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

        <div>
            <label for="remember">Remember me</label>
            <input type="checkbox" id="remember" name="remember">
        </div>
        <p>
            <input type="submit" id="login" value="Login">
        </p>
    </form>
    <div id="create-account-wrap">
        <p>Not a member? <a href="{{ route('register') }}">Create Account</a>
        <p>
    </div>
    <!--create-account-wrap-->
@endsection
