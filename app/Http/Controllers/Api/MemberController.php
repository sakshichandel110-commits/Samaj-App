<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;
use App\Models\IdentityDocument;
use App\Models\FamilyMember;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Community;
use App\Models\MatrimonyPost;
use App\Models\PostReaction;
use App\Models\Announcement;
use App\Models\AnnouncementReaction;
use Illuminate\Support\Facades\Schema;

class MemberController extends Controller
{
    /**
     * Store member with identity document and family members.
     *
     * @param StoreMemberRequest $request
     * @return JsonResponse
     */
    public function store(StoreMemberRequest $request)
    {

        $data = $request->only([
            'full_name',
            'gender',
            'date_of_birth',
            'mobile_number',
            'email',
            'marital_status',
            'surname',
            'gotra',
            'native_place',
            'address_line_1',
            'address_line_2',
            'city',
            'state',
            'pincode'
        ]);

        // Require authenticated user (route middleware 'auth:sanctum' should provide this)
        if (!auth()->check()) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 400);
        }

        // Ensure user does not already have a verification
        $userId = auth()->id();
        $existing = Member::where('user_id', $userId)->first();
        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'User already has a verification',
                'data' => $existing
            ], 409);
        }

        // Create member and attach to authenticated user
        $member = new Member($data);
        $member->user_id = $userId;
        $member->save();

        // Handle identity document
        if ($request->hasFile('document_image')) {
            $file = $request->file('document_image');
            $path = $file->store('documents', 'public');

            IdentityDocument::create([
                'member_id' => $member->id,
                'document_type' => $request->input('document_type'),
                'document_path' => $path,
            ]);
        }

        // Handle family members array
        $family = $request->input('family_members', []);
        foreach ($family as $fm) {
            $fmData = array_merge($fm, ['member_id' => $member->id]);
            FamilyMember::create($fmData);
        }

        $member->load('identityDocuments', 'familyMembers');

        // Add full public URL for each identity document (e.g., Aadhaar image)
        $member->identityDocuments->transform(function ($doc) {
            $doc->document_url = $doc->document_path ? Storage::disk('public')->url($doc->document_path) : null;
            return $doc;
        });

        return response()->json([
            'status' => true,
            'message' => 'Member verification request created successfully',
            'data' => $member,
        ], 200);
    }

    /**
     * Create a matrimony post.
     * Accepts: name, age, occupation, bio, image (file)
     */
    public function createPost(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'occupation' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('matrimony_images', 'public');
        }

        $post = MatrimonyPost::create([
            'user_id' => $user->id,
            'name' => $validated['name'] ?? null,
            'age' => $validated['age'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'image_path' => $imagePath,
        ]);

        return response()->json(['status' => true, 'message' => 'Post created', 'data' => $post], 201);
    }

    /**
     * Like/share a post. Body: post_id, liked (bool), shared (bool)
     */
    public function reactToPost(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();

        $validated = $request->validate([
            'post_id' => 'required|integer|exists:matrimony_posts,id',
            'liked' => 'nullable|boolean',
            'shared' => 'nullable|boolean',
        ]);

        $post = MatrimonyPost::find($validated['post_id']);

        $reaction = PostReaction::firstOrNew([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $prevLiked = (bool) ($reaction->liked ?? false);
        $prevShared = (bool) ($reaction->shared ?? false);

        $newLiked = array_key_exists('liked', $validated) ? (bool)$validated['liked'] : $prevLiked;
        $newShared = array_key_exists('shared', $validated) ? (bool)$validated['shared'] : $prevShared;

        // update counts
        if ($prevLiked !== $newLiked) {
            $post->likes_count = $post->likes_count + ($newLiked ? 1 : -1);
            if ($post->likes_count < 0) $post->likes_count = 0;
        }
        if ($prevShared !== $newShared) {
            $post->shares_count = $post->shares_count + ($newShared ? 1 : -1);
            if ($post->shares_count < 0) $post->shares_count = 0;
        }

        $reaction->liked = $newLiked;
        $reaction->shared = $newShared;
        $reaction->save();

        $post->save();

        return response()->json(['status' => true, 'message' => 'Reaction updated', 'data' => [
            'post' => $post,
            'reaction' => $reaction
        ]], 200);
    }

    /**
     * Create an announcement.
     * Accepts: type, title (required), description, image (file)
     */
    public function createAnnouncement(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();

        $validated = $request->validate([
            'type' => 'nullable|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcements_images', 'public');
        }

        $announcement = Announcement::create([
            'user_id' => $user->id,
            'type' => $validated['type'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
        ]);

        return response()->json(['status' => true, 'message' => 'Announcement created', 'data' => $announcement], 201);
    }

    /**
     * Like/share an announcement. Body: announcement_id, liked (bool), shared (bool)
     */
    public function reactToAnnouncement(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();

        $validated = $request->validate([
            'announcement_id' => 'required|integer|exists:announcements,id',
            'liked' => 'nullable|boolean',
            'shared' => 'nullable|boolean',
        ]);

        $announcement = Announcement::find($validated['announcement_id']);

        $reaction = AnnouncementReaction::firstOrNew([
            'user_id' => $user->id,
            'announcement_id' => $announcement->id,
        ]);

        $prevLiked = (bool) ($reaction->liked ?? false);
        $prevShared = (bool) ($reaction->shared ?? false);

        $newLiked = array_key_exists('liked', $validated) ? (bool)$validated['liked'] : $prevLiked;
        $newShared = array_key_exists('shared', $validated) ? (bool)$validated['shared'] : $prevShared;

        // update counts
        if ($prevLiked !== $newLiked) {
            $announcement->likes_count = $announcement->likes_count + ($newLiked ? 1 : -1);
            if ($announcement->likes_count < 0) $announcement->likes_count = 0;
        }
        if ($prevShared !== $newShared) {
            $announcement->shares_count = $announcement->shares_count + ($newShared ? 1 : -1);
            if ($announcement->shares_count < 0) $announcement->shares_count = 0;
        }

        $reaction->liked = $newLiked;
        $reaction->shared = $newShared;
        $reaction->save();

        $announcement->save();

        return response()->json(['status' => true, 'message' => 'Reaction updated', 'data' => [
            'announcement' => $announcement,
            'reaction' => $reaction
        ]], 200);
    }

    /**
     * List matrimony posts for the authenticated user's community.
     * Returns count and array of posts with image URL and current user's reaction.
     */
    public function listPosts(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();
        $communityId = $user->community_id ?? null;
        if (empty($communityId)) {
            return response()->json(['status' => true, 'message' => 'No community assigned', 'count' => 0, 'data' => []], 200);
        }

        $perPage = (int) $request->input('per_page', 15);
        $page = (int) $request->input('page', 1);

        $query = MatrimonyPost::with('user')
            ->whereHas('user', function ($q) use ($communityId) {
                $q->where('community_id', $communityId);
            });

        $paginated = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        $postIds = collect($paginated->items())->pluck('id')->all();
        $reactions = PostReaction::whereIn('post_id', $postIds)->where('user_id', $user->id)->get()->keyBy('post_id');

        $data = collect($paginated->items())->map(function ($p) use ($reactions) {
            $r = $reactions->get($p->id);
            return [
                'id' => $p->id,
                'name' => $p->name,
                'age' => $p->age,
                'occupation' => $p->occupation,
                'bio' => $p->bio,
                'image_path' => $p->image_path,
                'likes_count' => $p->likes_count,
                'shares_count' => $p->shares_count,
                'created_at' => $p->created_at,
                'user' => [
                    'id' => $p->user->id,
                    'name' => $p->user->name ?? trim(($p->user->first_name ?? '') . ' ' . ($p->user->last_name ?? '')),
                ],
                'user_reaction' => $r ? ['liked' => (bool)$r->liked, 'shared' => (bool)$r->shared] : ['liked' => false, 'shared' => false],
            ];
        })->all();

        return response()->json([
            'status' => true,
            'message' => 'Posts fetched',
            'count' => $paginated->total(),
            'per_page' => $paginated->perPage(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'data' => $data,
        ], 200);
    }

    /**
     * List announcements for the authenticated user's community.
     */
    public function listAnnouncements(Request $request)
    {
        if (! auth()->check()) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();
        $communityId = $user->community_id ?? null;
        if (empty($communityId)) {
            return response()->json(['status' => true, 'message' => 'No community assigned', 'count' => 0, 'data' => []], 200);
        }

        $perPage = (int) $request->input('per_page', 15);
        $page = (int) $request->input('page', 1);

        $query = Announcement::with('user')
            ->whereHas('user', function ($q) use ($communityId) {
                $q->where('community_id', $communityId);
            });

        $paginated = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

        $announcementIds = collect($paginated->items())->pluck('id')->all();
        $reactions = AnnouncementReaction::whereIn('announcement_id', $announcementIds)->where('user_id', $user->id)->get()->keyBy('announcement_id');

        $data = collect($paginated->items())->map(function ($a) use ($reactions) {
            $r = $reactions->get($a->id);
            return [
                'id' => $a->id,
                'type' => $a->type,
                'title' => $a->title,
                'description' => $a->description,
                'image_path' => $a->image_path,
                'likes_count' => $a->likes_count,
                'shares_count' => $a->shares_count,
                'created_at' => $a->created_at,
                'user' => [
                    'id' => $a->user->id,
                    'name' => $a->user->name ?? trim(($a->user->first_name ?? '') . ' ' . ($a->user->last_name ?? '')),
                ],
                'user_reaction' => $r ? ['liked' => (bool)$r->liked, 'shared' => (bool)$r->shared] : ['liked' => false, 'shared' => false],
            ];
        })->all();

        return response()->json([
            'status' => true,
            'message' => 'Announcements fetched',
            'count' => $paginated->total(),
            'per_page' => $paginated->perPage(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'data' => $data,
        ], 200);
    }

    public function joinCommunity(Request $request)
    {

        $user = $request->user();
        if (! $user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'code' => 'required|string'
        ]);

        $community = Community::where('code', $validated['code'])->first();
        if (! $community) {
            return response()->json(['status' => false, 'message' => 'Invalid community code'], 404);
        }

        if (! empty($user->community_id) && $user->community_id != $community->id) {
            return response()->json(['status' => false, 'message' => 'User already belongs to another community'], 409);
        }

        if ($user->community_id == $community->id) {
            return response()->json(['status' => true, 'message' => 'Already a member of this community', 'data' => $community], 200);
        }

        $user->community_id = $community->id;
        $user->save();

        return response()->json(['status' => true, 'message' => 'Joined community successfully', 'data' => $community], 200);
    }
}
