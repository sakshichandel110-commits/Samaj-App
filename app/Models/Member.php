<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'gender', 'date_of_birth', 'mobile_number', 'email', 'marital_status',
        'surname', 'gotra', 'native_place',
        'address_line_1', 'address_line_2', 'city', 'state', 'pincode'
    ];

    protected $dates = ['date_of_birth'];

    public function identityDocuments()
    {
        return $this->hasMany(IdentityDocument::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class);
    }
}
