<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <style>
        .error-message {
            color: red;
            background-color: #ffeeee;
            border: 1px solid red;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Welcome to the homepage!</h1>

    @if (session('error'))
        <div class="error-message">
            {{ session('error') }}
        </div>
    @endif

    <ul>
        <li><a href="/restricted?age=17">Restricted Page (Age 17)</a></li>
        <li><a href="/restricted?age=20">Restricted Page (Age 20)</a></li>
        <li><a href="/adult-store?age=17">Adult Store Page (Age 17)</a></li>
        <li><a href="/adult-store?age=20">Adult Store Page (Age 20)</a></li>
        <li><a href="/tags?id=1">Tags</a></li>
        <li><a href="/posts?id=1">Posts</a></li>
        <li><a href="/users?id=1">Users</a></li>
        <li><a href="/tags?id=2">Unauthorized Tags</a></li>
        <li><a href="/posts?id=2">Unauthorized Posts</a></li>
        <li><a href="/users?id=2">Unauthorized Users</a></li>
        <li><a href="/users/1?id=1">User Details</a></li>
    </ul>

</body>
</html>
