<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $table = 'otp_verifications';
    protected $fillable = ['mobile', 'otp', 'expires_at'];
    public $timestamps = false;
    protected $dates = ['expires_at'];
}
