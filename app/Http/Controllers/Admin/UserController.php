<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('manageUsers');

        $users = User::query()
            ->orderBy('name')
            ->paginate(20);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }
}
