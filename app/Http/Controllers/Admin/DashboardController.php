<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('accessAdmin');

        $totalUsers = User::query()->count();
        $totalPosts = Post::query()->count();

        $recentPosts = Post::query()
            ->with('user')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalPosts' => $totalPosts,
            'recentPosts' => $recentPosts,
        ]);
    }
}
