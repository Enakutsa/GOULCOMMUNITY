<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory;

    protected $table = 'prospects';

    protected $fillable = [
        'nom', 'prenom', 'email', 'telephone', 'profil', 'etablissement_interesse',
        'consentement_information', 'consentement_promotionnel', 'consentement_at',
        'token_desabonnement', 'desabonne_at',
    ];

    protected $casts = [
        'consentement_information' => 'boolean',
        'consentement_promotionnel' => 'boolean',
        'consentement_at' => 'datetime',
        'desabonne_at' => 'datetime',
    ];

    protected $hidden = ['token_desabonnement'];
}