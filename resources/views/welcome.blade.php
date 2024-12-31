<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Socialite Testing Example</title>
</head>
<body>
    <h1>Laravel Socialite Testing Example</h1>
    @if (auth()->check())
        <p>User is authenticated.</p>
        <p>Name: {{ auth()->user()->name }}</p>
        <p>Email: {{ auth()->user()->email }}</p>
        <p><a href="/auth/logout">Logout</a></p>
    @else
        <p>User is not authenticated.</p>
        <p>
            <a href="/auth/google/redirect">Login with Google</a>
        </p>
    @endif
</body>
</html>
