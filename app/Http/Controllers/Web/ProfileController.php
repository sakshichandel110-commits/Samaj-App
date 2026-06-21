<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'                   => 'nullable|string|max:191',
            'dob'                    => 'nullable|string|max:30',
            'address'                => 'nullable|string|max:1000',
            'bio'                    => 'nullable|string|max:2000',
            'education'              => 'nullable|string|max:1000',
            'website'                => 'nullable|url|max:255',
            'profession_designation' => 'nullable|string|max:191',
            'profile_image'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $data = $request->only([
            'name', 'dob', 'address', 'bio',
            'education', 'website', 'profession_designation',
        ]);

        // Remove nulls so we don't overwrite existing values with empty strings
        $data = array_filter($data, fn($v) => $v !== null && $v !== '');

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')
                ->store('profile_images', 'public');
        }

        $user->update($data);

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }
}
