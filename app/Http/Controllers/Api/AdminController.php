<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Community;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
	/**
	 * Store a new community. User must be admin type.
	 */
	public function storeCommunity(Request $request)
	{
		$user = $request->user();

		if (! $user || strtolower(($user->user_type ?? '')) !== 'admin') {
			return response()->json(['status' => false, 'message' => 'Unauthorized. Only admin users can perform this action.'], 403);
		}

		/**
		 * Admin verification endpoint.
		 * - If no body provided: returns list of users with status 'pending' and their full details.
		 * - If `user_id` and `status` provided: update that user's status (accepted/rejected/pending).
		 */
		

		/**
		 * Join a community by its code. Authenticated user will be assigned the community_id.
		 */
		

		// Ensure a user can create only one community
		if (Community::where('user_id', $user->id)->exists()) {
			return response()->json(['status' => false, 'message' => 'User already has a community'], 409);
		}

		

		$validated = $request->validate([
			'community_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
			'community_name' => 'required|string|max:255',
			'community_short_name' => 'required|string|max:255',
			'community_description' => 'nullable|string',
			'admin_designation' => 'nullable|string|max:255',
		]);

		// handle logo upload
		$logoPath = null;
		if ($request->hasFile('community_logo')) {
			$logoPath = $request->file('community_logo')->store('community_logos', 'public');
		}

		// generate unique code
		$code = $this->generateUniqueCode($validated['community_short_name']);

		$community = Community::create([
			'user_id' => $user->id,
			'community_logo' => $logoPath,
			'community_name' => $validated['community_name'],
			'community_short_name' => $validated['community_short_name'],
			'community_description' => $validated['community_description'] ?? null,
			'admin_designation' => $validated['admin_designation'] ?? null,
			'code' => $code,
		]);

		// Assign the creating user to this community (one community per user)
		$user->community_id = $community->id;
		$user->save();

		return response()->json([
			'status' => true,
			'message' => 'Community created successfully',
			'data' => $community,
			'code' => $code,
		], 201);
	}

	/**
	 * Generate a short unique code for the community.
	 * Format: UPPERPREFIX + 4 random digits, e.g. RAJSUR4821
	 */
	protected function generateUniqueCode(string $shortName)
	{
		$clean = preg_replace('/[^A-Za-z]/', '', $shortName);
		$prefix = strtoupper(substr($clean, 0, 6));
		if (strlen($prefix) < 3) {
			$prefix = strtoupper(Str::random(3));
		}

		do {
			$digits = random_int(1000, 9999);
			$code = $prefix . $digits;
		} while (Community::where('code', $code)->exists());

		return $code;
	}

    public function verifyMember(Request $request)
		{
			$admin = $request->user();
			if (! $admin || strtolower(($admin->user_type ?? '')) !== 'admin') {
				return response()->json(['status' => false, 'message' => 'Unauthorized. Only admin users can perform this action.'], 403);
			}

			// list pending when no user_id provided
			$userId = $request->input('user_id');
			$status = $request->input('status');

			if (! $userId) {
				$pending = User::where('status', 'pending')->get();
				$result = $pending->map(function ($u) {
					return $u->fullDetails();
				})->all();

				return response()->json(['status' => true, 'count' => count($result), 'message' => 'Pending users fetched', 'data' => $result], 200);
			}

			// update user status
			if (! in_array(strtolower($status ?? ''), ['pending', 'accept', 'accepted', 'reject', 'rejected'])) {
				return response()->json(['status' => false, 'message' => 'Invalid status. Use accept/reject/pending'], 400);
			}

			$user = User::find($userId);
			if (! $user) {
				// Only show pending users that belong to the same community as the admin
				$adminCommunityId = $admin->community_id ?? null;
				if (empty($adminCommunityId)) {
					// admin not assigned to any community -> return empty list
					return response()->json(['status' => true, 'message' => 'Pending users fetched', 'data' => []], 200);
				}

				$pending = User::where('status', 'pending')
					->where('community_id', $adminCommunityId)
					->get();
				$result = $pending->map(function ($u) {
					return $u->fullDetails();
				})->all();

				return response()->json(['status' => true, 'message' => 'Pending users fetched', 'data' => $result], 200);
			if ($s === 'reject') $s = 'rejected';

			$user->status = $s;
			$user->save();

			return response()->json(['status' => true, 'message' => 'User status updated', 'data' => $user->fullDetails()], 200);
		}
}
