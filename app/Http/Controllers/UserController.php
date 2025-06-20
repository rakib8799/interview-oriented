<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('role.admin')->only(['index', 'show']);
    }

    public function index()
    {
        return "User List";
    }

    public function show(User $user)
    {
        return "User $user";
    }
}
