<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MatrimonyPost;
use App\Models\PostReaction;
use Illuminate\Http\Request;

class MatrimonyPostController extends Controller
{
    public function index(Request $request)
    {
        $query = MatrimonyPost::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('occupation', 'like', "%{$s}%")
                  ->orWhere('bio', 'like', "%{$s}%");
            });
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.matrimony-posts.index', compact('posts'));
    }

    public function show($id)
    {
        $post = MatrimonyPost::with(['user', 'reactions.user'])->findOrFail($id);
        return view('admin.matrimony-posts.show', compact('post'));
    }

    public function destroy($id)
    {
        $post = MatrimonyPost::findOrFail($id);
        PostReaction::where('post_id', $id)->delete();
        $post->delete();

        return redirect()->route('admin.matrimony-posts.index')
                         ->with('success', 'Matrimony post deleted successfully.');
    }
}
