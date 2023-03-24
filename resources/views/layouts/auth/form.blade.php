<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('auth/css/style.css') }}">
    <style>
        input~small {
            color: coral;
            display: block;
            text-align: left;
            font-size: 60%;
        }
    </style>
</head>

<body>
    <!-- partial:index.partial.html -->
    <div id="login-form-wrap">
        <h2>@yield('heading')</h2>

        @yield('body')

    </div>
    <!--login-form-wrap-->
    <!-- partial -->

</body>

</html>
