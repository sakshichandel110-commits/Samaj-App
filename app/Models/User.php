<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
 /*    protected $fillable = [
        'name',
        'email',
        'password',
    ]; */
    protected $guarded=[];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function members()
    {
        return $this->hasMany(\App\Models\Member::class);
    }

    public function community()
    {
        return $this->belongsTo(\App\Models\Community::class);
    }

    // convenience: assume single member record per user
    public function member()
    {
        return $this->hasOne(\App\Models\Member::class);
    }

    /**
     * Return full aggregated details for this user including member, identity documents, family members, and image URLs.
     */
    public function fullDetails()
    {
        $user = $this->toArray();

        // profile image url
        if (!empty($this->profile_image)) {
            $user['profile_image_url'] = asset('storage/' . $this->profile_image);
        } else {
            $user['profile_image_url'] = null;
        }

        $member = $this->member()->with(['identityDocuments', 'familyMembers'])->first();
        if ($member) {
            // start from a clean array and avoid duplicate snake_case keys
            $m = $member->toArray();
            unset($m['identity_documents'], $m['family_members']);

            // identity documents: use model accessor to get full document_path URL and also expose document_url
            $m['identityDocuments'] = collect($member->identityDocuments)->map(function ($doc) {
                $d = $doc->toArray();
                $d['document_url'] = $d['document_path'] ?? null;
                return $d;
            })->all();

            // family members
            $m['familyMembers'] = $member->familyMembers->toArray();

            $user['member'] = $m;
        } else {
            $user['member'] = null;
        }

        // community info
        $user['community'] = $this->community ? $this->community->toArray() : null;

        return $user;
    }
}
