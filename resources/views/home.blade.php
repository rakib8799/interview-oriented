<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h2>Homepage</h2>
    <ul>
        <li><a href="{{ route('about') }}">About</a></li>
        <li><a href="{{ route('users.index') }}">Users</a></li>
    </ul>
</body>
</html>
