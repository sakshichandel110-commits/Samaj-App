<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Community;
use App\Models\Member;
use App\Models\Announcement;
use App\Models\MatrimonyPost;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'         => User::count(),
            'admin_users'         => User::where('user_type', 'admin')->count(),
            'member_users'        => User::where('user_type', 'member')->count(),
            'pending_members'     => User::where('status', 'pending')->count(),
            'accepted_members'    => User::where('status', 'accepted')->count(),
            'total_communities'   => Community::count(),
            'total_announcements' => Announcement::count(),
            'total_posts'         => MatrimonyPost::count(),
            'total_members'       => Member::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
