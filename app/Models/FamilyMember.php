<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = ['member_id', 'name', 'relation', 'mobile_number', 'designation', 'education'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
