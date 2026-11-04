<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etablissement extends Model
{
    use HasFactory;

    protected $table = 'etablissements';

    protected $fillable = [
        'nom', 'sigle', 'slug', 'type', 'secteur', 'description', 'ville', 'quartier',
        'adresse', 'site_web', 'email_contact', 'telephone', 'logo_url',
        'statut_page', 'est_publie',
    ];

    protected $casts = [
        'est_publie' => 'boolean',
    ];

    public function formations(): HasMany
    {
        return $this->hasMany(Formation::class, 'etablissement_id');
    }

    public function scopePublie($query)
    {
        return $query->where('est_publie', true);
    }
}