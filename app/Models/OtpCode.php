<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $table = 'otp_codes';

    protected $fillable = [
        'email',
        'code_hash',
        'code_dev',
        'expire_at',
        'tentatives',
        'utilise_at',
    ];

    protected $casts = [
        'expire_at' => 'datetime',
        'utilise_at' => 'datetime',
    ];

    public function estExpire(): bool
    {
        return $this->expire_at->isPast();
    }

    public function estUtilise(): bool
    {
        return $this->utilise_at !== null;
    }

    public function tropDeTentatives(): bool
    {
        return $this->tentatives >= 5;
    }
}