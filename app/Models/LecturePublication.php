<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LecturePublication extends Model
{
    protected $table = 'lectures_publications';

    public $timestamps = false;

    protected $fillable = [
        'utilisateur_id',
        'publication_id',
    ];

    protected $casts = [
        'lu_at' => 'datetime',
    ];
}