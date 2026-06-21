<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::with(['user.community']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                  ->orWhere('mobile_number', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->whereHas('user', fn($q) => $q->where('status', $request->status));
        }

        $members = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    public function show($id)
    {
        $member = Member::with(['identityDocuments', 'familyMembers', 'user.community'])
                        ->findOrFail($id);

        return view('admin.members.show', compact('member'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $member = Member::with('user')->findOrFail($id);
        if ($member->user) {
            $member->user->status = $request->status;
            $member->user->save();
        }

        return back()->with('success', 'Member status updated to ' . $request->status . '.');
    }
}
