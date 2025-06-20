<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // chunk large datasets for part by part load
        // $allUsers = [];
        // User::chunk(100, function ($users) use (&$allUsers) {
        //     foreach ($users as $user) {
        //         $allUsers[] = $user;
        //     }
        // });
        // return response()->json($allUsers);

        // $users = User::all();
        $users = User::paginate(2);
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request['password'] = '12345';
        $user = User::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ]);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $isUpdated = $user->update($request->all());
        if($isUpdated) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        }
    }

    public function destroy(User $user)
    {
        $isDeleted = $user->delete();
        if($isDeleted) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        }
    }
}
