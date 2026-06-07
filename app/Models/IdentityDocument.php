<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class IdentityDocument extends Model
{
    use HasFactory;

    protected $fillable = ['member_id', 'document_type', 'document_path'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Always return full public URL for document_path when accessed.
     */
    public function getDocumentPathAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }
}
