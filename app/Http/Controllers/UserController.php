<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = DB::table('users')->get();
        // $users = DB::table('users')->where('id', '>', 2)->get();
        // $users = DB::table('users')->select(['name', 'email'])->get();
        // $users = DB::table('users')->pluck('name')->toArray();

        // Aggregate functions
        // count, max, min, sum, avg
        // $users = DB::table('users')->count();
        // $users = DB::table('users')->max('salary');
        // $users = DB::table('users')->min('salary');
        // $users = DB::table('users')->sum('salary');
        // $users = DB::table('users')->avg('salary');

        // Joins
        // Inner Join
        // $users = DB::table('users')
        // ->join('orders', 'users.id', '=', 'orders.user_id')
        // ->select('users.name', 'users.email', 'orders.user_id', 'orders.product_name', 'orders.total_amount')
        // ->get();

        // Left Join
        // $users = DB::table('users')
        // ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
        // ->select('users.name', 'orders.product_name')
        // ->get();

        // Right Join
        // $users = DB::table('orders')
        // ->rightJoin('users', 'users.id', '=', 'orders.user_id')
        // ->select('orders.product_name', 'users.name')
        // ->get();

        // Full join
        // $users = DB::table('users')
        // ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
        // ->select('users.name', 'orders.product_name')
        // ->union(
        //     DB::table('orders')
        //     ->rightJoin('users', 'users.id', '=', 'orders.user_id')
        //     ->select('orders.product_name', 'users.name')
        // )->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request['password'] = Hash::make('12345');
        $user = DB::table('users')->insert($request->all());
        if($user) {
            return response()->json([
                'success' => true,
                'message' => "User created successfully"
            ], 201);
        }
    }

    public function update(Request $request, User $user)
    {
        $user = DB::table('users')->where('id', $user->id)->update($request->all());
        if($user) {
            return response()->json([
                'success' => true,
                'message' => "User updated successfully"
            ]);
        }
    }

    public function destroy(User $user)
    {
        $user = DB::table('users')->where('id', $user->id)->delete();
        if($user) {
            return response()->json([
                'success' => true,
                'message' => "User deleted successfully"
            ]);
        }
    }
}
