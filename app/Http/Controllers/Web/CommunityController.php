<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $query = Community::with('user');
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('community_name', 'like', "%$s%")
                  ->orWhere('community_short_name', 'like', "%$s%")
                  ->orWhere('code', 'like', "%$s%");
            });
        }
        $communities = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        return view('admin.communities.index', compact('communities'));
    }

    public function create()
    {
        $admins = User::where('user_type', 'admin')->orderBy('name')->get();
        return view('admin.communities.create', compact('admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'community_name'        => 'required|string|max:255',
            'community_short_name'  => 'required|string|max:255',
            'community_description' => 'nullable|string',
            'admin_designation'     => 'nullable|string|max:255',
            'community_logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id'               => 'required|exists:users,id',
        ]);

        $logoPath = null;
        if ($request->hasFile('community_logo')) {
            $logoPath = $request->file('community_logo')->store('community_logos', 'public');
        }

        // Generate unique code
        $short  = preg_replace('/[^A-Za-z]/', '', $request->community_short_name);
        $prefix = strtoupper(substr($short, 0, 6));
        if (strlen($prefix) < 3) $prefix = strtoupper(Str::random(3));
        do { $code = $prefix . random_int(1000, 9999); } while (Community::where('code', $code)->exists());

        $community = Community::create([
            'user_id'               => $request->user_id,
            'community_name'        => $request->community_name,
            'community_short_name'  => $request->community_short_name,
            'community_description' => $request->community_description,
            'admin_designation'     => $request->admin_designation,
            'community_logo'        => $logoPath,
            'code'                  => $code,
        ]);

        // Assign community to owner user
        User::where('id', $request->user_id)->update(['community_id' => $community->id]);

        return redirect()->route('admin.communities.show', $community->id)
                         ->with('success', 'Community created successfully! Code: ' . $code);
    }

    public function show($id)
    {
        $community = Community::with('user')->findOrFail($id);
        $members   = User::where('community_id', $id)->with('member')->paginate(10);
        return view('admin.communities.show', compact('community', 'members'));
    }

    public function edit($id)
    {
        $community = Community::findOrFail($id);
        $admins    = User::where('user_type', 'admin')->orderBy('name')->get();
        return view('admin.communities.edit', compact('community', 'admins'));
    }

    public function update(Request $request, $id)
    {
        $community = Community::findOrFail($id);

        $request->validate([
            'community_name'        => 'required|string|max:255',
            'community_short_name'  => 'required|string|max:255',
            'community_description' => 'nullable|string',
            'admin_designation'     => 'nullable|string|max:255',
            'community_logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_id'               => 'required|exists:users,id',
        ]);

        $data = $request->only(['community_name', 'community_short_name', 'community_description', 'admin_designation', 'user_id']);

        if ($request->hasFile('community_logo')) {
            if ($community->getRawOriginal('community_logo')) {
                Storage::disk('public')->delete($community->getRawOriginal('community_logo'));
            }
            $data['community_logo'] = $request->file('community_logo')->store('community_logos', 'public');
        }

        $community->update($data);

        return redirect()->route('admin.communities.show', $community->id)
                         ->with('success', 'Community updated successfully.');
    }

    public function destroy($id)
    {
        $community = Community::findOrFail($id);
        // Remove community from all users
        User::where('community_id', $id)->update(['community_id' => null]);
        // Delete logo
        if ($community->getRawOriginal('community_logo')) {
            Storage::disk('public')->delete($community->getRawOriginal('community_logo'));
        }
        $community->delete();
        return redirect()->route('admin.communities.index')
                         ->with('success', 'Community deleted successfully.');
    }
}
