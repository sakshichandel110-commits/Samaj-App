<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MatrimonyPost extends Model
{
    use HasFactory;

    protected $table = 'matrimony_posts';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reactions()
    {
        return $this->hasMany(PostReaction::class, 'post_id');
    }

    // Always return full public URL for image_path
    public function getImagePathAttribute($value)
    {
        if (empty($value)) return null;
        if (filter_var($value, FILTER_VALIDATE_URL)) return $value;
        return Storage::disk('public')->url($value);
    }
}
