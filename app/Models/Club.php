<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Club extends Model
{
    protected $table = 'clubs';

    protected $fillable = [
        'etablissement_id',
        'createur_id',
        'nom',
        'slug',
        'description',
        'logo_url',
        'categorie',
        'est_officiel',
        'statut_page',
        'est_actif',
    ];

    protected $casts = [
        'est_officiel' => 'boolean',
        'est_actif' => 'boolean',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'createur_id');
    }
}