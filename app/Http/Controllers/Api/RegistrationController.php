<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class RegistrationController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:member,admin',
            'login_type' => 'required|in:mobile,google',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->messages()
            ], 200);
        }

        if ($request->login_type == 'mobile') {

            $validator = Validator::make($request->all(), [
                'mobile' => 'required|digits:10'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()->messages()
                ], 200);
            }

            $otp = rand(100000, 999999);

            OtpVerification::updateOrCreate(
                ['mobile' => $request->mobile],
                [
                    'otp' => $otp,
                    'expires_at' => now()->addMinutes(5)
                ]
            );

            // Send OTP via WhatsApp/SMS

            return response()->json([
                'status' => true,
                'otp' => $otp, // For testing, remove in production
                'message' => 'OTP sent successfully'
            ]);
        }

        if ($request->login_type == 'google') {

            $validator = Validator::make($request->all(), [
                'google_token' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()->messages()
                ], 200);
            }

            // Verify Google token

            $googleUser = $this->verifyGoogleToken(
                $request->google_token
            );

            if (!$googleUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Google token'
                ]);
            }

            $user = User::where('email', $googleUser['email'])->first();
            $isNew = false;
            if (!$user) {
                $data = ['email' => $googleUser['email'], 'user_type' => $request->user_type];
                if (Schema::hasColumn('users', 'name')) {
                    $data['name'] = $googleUser['name'];
                } else {
                    // split name into first/last if possible
                    $parts = explode(' ', $googleUser['name'], 2);
                    if (Schema::hasColumn('users', 'first_name')) {
                        $data['first_name'] = $parts[0] ?? null;
                    }
                    if (Schema::hasColumn('users', 'last_name')) {
                        $data['last_name'] = $parts[1] ?? null;
                    }
                }

                // ensure password and email exist (DB requires them)
                if (empty($data['email'])) {
                    $data['email'] = 'user_' . uniqid() . '@no-reply.local';
                }
                if (empty($data['password'])) {
                    $data['password'] = Hash::make(Str::random(40));
                }

                $user = User::create($data);
                $isNew = true;
            }
            $token = $user->createToken('auth')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => $isNew ? 'register successful' : 'login successful',
                'token' => $token,
                'user' => $user
            ]);
        }
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'otp' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->messages()
            ], 200);
        }

        $otpData = OtpVerification::where(
            'mobile',
            $request->mobile
        )->first();

        if (
            !$otpData ||
            $otpData->otp != $request->otp ||
            $otpData->expires_at < now()
        ) {

            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ]);
        }

        // try to locate existing user by mobile/number
        $isNew = false;
        $user = User::where(function ($q) use ($request) {
            if (Schema::hasColumn('users', 'mobile')) {
                $q->orWhere('mobile', $request->mobile);
            }
            if (Schema::hasColumn('users', 'number')) {
                $q->orWhere('number', $request->mobile);
            }
        })->first();

        if (!$user) {
            $data = ['user_type' => 'member'];
            if (Schema::hasColumn('users', 'mobile')) {
                $data['mobile'] = $request->mobile;
            }
            if (Schema::hasColumn('users', 'number') && !isset($data['number'])) {
                $data['number'] = $request->mobile;
            }
            if (empty($data['password'])) {
                $data['password'] = Hash::make(Str::random(40));
            }

            $user = User::create($data);
            $isNew = true;
        }

        $token = $user->createToken('auth')->plainTextToken;

        $otpData->delete();

        return response()->json([
            'status' => true,
            'message' => $isNew ? 'register successful' : 'login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * Exchange Google OAuth authorization code for tokens and sign the user in.
     * Expects `code` (required) and optional `redirect_uri` in the request body.
     */
    public function exchangeGoogleCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'redirect_uri' => 'nullable|url'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->messages()
            ], 200);
        }

        $clientId = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $redirect = $request->input('redirect_uri', env('GOOGLE_REDIRECT_URI'));

        if (empty($clientId) || empty($clientSecret) || empty($redirect)) {
            return response()->json(['status' => false, 'message' => 'Google client config (GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, GOOGLE_REDIRECT_URI) must be set'], 500);
        }

        try {
            $resp = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $request->code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirect,
                'grant_type' => 'authorization_code',
            ]);

            if (!$resp->successful()) {
                return response()->json(['status' => false, 'message' => 'Token exchange failed', 'details' => $resp->body()], 400);
            }

            $body = $resp->json();
            $idToken = $body['id_token'] ?? null;

            if (!$idToken) {
                return response()->json(['status' => false, 'message' => 'No id_token returned by Google', 'response' => $body], 400);
            }

            $googleUser = $this->verifyGoogleToken($idToken);
            if (!$googleUser) {
                return response()->json(['status' => false, 'message' => 'Invalid id_token or failed to verify Google token'], 400);
            }

            // find or create user
            $user = User::where('email', $googleUser['email'])->first();
            if (!$user) {
                $data = ['email' => $googleUser['email'], 'user_type' => 'member'];
                if (Schema::hasColumn('users', 'name')) {
                    $data['name'] = $googleUser['name'] ?? null;
                } else {
                    $parts = explode(' ', $googleUser['name'] ?? '', 2);
                    if (Schema::hasColumn('users', 'first_name')) {
                        $data['first_name'] = $parts[0] ?? null;
                    }
                    if (Schema::hasColumn('users', 'last_name')) {
                        $data['last_name'] = $parts[1] ?? null;
                    }
                }

                if (empty($data['password'])) {
                    $data['password'] = Hash::make(Str::random(40));
                }

                    $user = User::create($data);
                    $isNew = true;
            }
                $token = $user->createToken('auth')->plainTextToken;

                return response()->json(['status' => true, 'message' => isset($isNew) && $isNew ? 'register successful' : 'login successful', 'token' => $token, 'user' => $user, 'id_token' => $idToken]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Exception during token exchange', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verify Google ID token using Google's tokeninfo endpoint.
     * Returns array with 'email' and 'name' on success, or null on failure.
     */
    private function verifyGoogleToken($idToken)
    {
        if (empty($idToken)) {
            return null;
        }

        try {
            $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $idToken,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            // Optional: verify audience if GOOGLE_CLIENT_ID is set
            $clientId = env('GOOGLE_CLIENT_ID');
            if ($clientId && isset($data['aud']) && $data['aud'] !== $clientId) {
                return null;
            }

            // Ensure email present and (if provided) verified
            if (empty($data['email'])) {
                return null;
            }

            if (isset($data['email_verified']) && !in_array($data['email_verified'], [true, 'true', '1', 1], true)) {
                return null;
            }

            return [
                'email' => $data['email'],
                'name' => $data['name'] ?? trim(($data['given_name'] ?? '') . ' ' . ($data['family_name'] ?? '')),
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Update or view authenticated user's profile.
     * - If `profile_view` == 1 in request: return user details (no update).
     * - Otherwise: validate and update allowed profile fields.
     * Authentication: requires Sanctum auth token (middleware on route).
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        // If client requested only to view profile, return it
        if ($request->has('profile_view') && intval($request->input('profile_view')) === 1) {
            $u = $user->toArray();
            if (!empty($u['profile_image'])) {
                $u['profile_image_url'] = asset('storage/' . $u['profile_image']);
            } else {
                $u['profile_image_url'] = null;
            }

            return response()->json([
                'status' => true,
                'message' => 'profile fetched',
                'user' => $u
            ]);
        }

        // Validate incoming profile fields; return 200 with errors if validation fails
        $validator = Validator::make($request->all(), [
            'profile_image' => 'nullable|image|max:5120', // max 5MB
            'name' => 'nullable|string|max:191',
            // accept dob as string; we'll parse common formats below
            'dob' => 'nullable',
            'address' => 'nullable|string|max:1000',
            'bio' => 'nullable|string|max:2000',
            'education' => 'nullable|string|max:1000',
            // allow other custom fields like social links
            'website' => 'nullable|url'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->messages()
            ], 200);
        }

        $data = [];
        foreach (['name', 'dob', 'address', 'bio', 'education', 'website'] as $f) {
            if ($request->has($f)) {
                $data[$f] = $request->input($f);
            }
        }

        

        // handle profile image upload if provided
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $path = $file->store('profile_images', 'public');
            // store the storage path
            $data['profile_image'] = $path;
        }

        if (!empty($data)) {
            $user->update($data);
        }

        $fresh = $user->fresh()->toArray();
        if (!empty($fresh['profile_image'])) {
            $fresh['profile_image_url'] = asset('storage/' . $fresh['profile_image']);
        } else {
            $fresh['profile_image_url'] = null;
        }

        return response()->json([
            'status' => true,
            'message' => 'profile updated',
            'user' => $fresh
        ]);
    }
}
