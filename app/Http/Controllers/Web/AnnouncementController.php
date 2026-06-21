<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementReaction;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('type', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $announcements = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $types = Announcement::select('type')->distinct()->whereNotNull('type')->pluck('type');

        return view('admin.announcements.index', compact('announcements', 'types'));
    }

    public function show($id)
    {
        $announcement = Announcement::with(['user', 'reactions.user'])->findOrFail($id);
        return view('admin.announcements.show', compact('announcement'));
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        // Delete reactions first
        AnnouncementReaction::where('announcement_id', $id)->delete();
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
                         ->with('success', 'Announcement deleted successfully.');
    }
}
