<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
</head>
<body>
    <h2>Required Users</h2>

    @php
        $users = [
            [
                'id' => 1,
                'slug' => 'jidu-1'
            ],
            [
                'id' => 2,
                'slug' => 'jidu-2'
            ],
            [
                'id' => 3,
                'slug' => 'jidu-3'
            ],
            [
                'id' => 4,
                'slug' => 'jidu-4'
            ],
            [
                'id' => 5,
                'slug' => 'jidu-5'
            ]
        ];
    @endphp

    @foreach ($users as $user)
        <ul>
            <li><a href="{{ route('users.user.show', [$user['id'], $user['slug']]) }}">User {{ $user['id'] }}</a></li>
        </ul>
    @endforeach

    <br>
    <hr>
    <h2>Optional Users</h2>
    <ul>
        <li><a href="{{ route('users.user.optional.show', [$user['id'], $user['slug']]) }}">User {{ $user['id'] }}</a></li>
    </ul>
</body>
</html>
