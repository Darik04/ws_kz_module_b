<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.css')}}">
    <link rel="stylesheet" href="{{asset('css/main.css')}}">
</head>
<body>
<header class="header">
    <div class="container h-container">
        <a href="/" class="h-logo">WS Kazakhstan</a>
        @if(\Illuminate\Support\Facades\Auth::check())
            <a class="logout-a" href="/logout">Logout</a>
        @else
            <div class="d-flex">
                <a class="logout-a mx-3" href="/login">Login</a>
                <a class="logout-a" href="/register">Register</a>
            </div>
        @endif
    </div>
</header>
@yield('content')


<script src="{{asset('js/bootstrap.js')}}"></script>
</body>
</html>
