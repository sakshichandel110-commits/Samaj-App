<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Announcement extends Model
{
    use HasFactory;

    protected $table = 'announcements';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->hasMany(AnnouncementReaction::class, 'announcement_id');
    }

    public function getImagePathAttribute($value)
    {
        if (empty($value)) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;
        return Storage::disk('public')->url($value);
    }
}
