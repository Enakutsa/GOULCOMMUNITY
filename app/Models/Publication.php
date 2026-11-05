<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publication extends Model
{
    protected $table = 'publications';

    public const STATUT_BROUILLON = 'brouillon';
    public const STATUT_PUBLIEE = 'publiee';
    public const STATUT_RETIREE = 'retiree';

    public const TYPES = [
        'actualite',
        'annonce',
        'info_pratique',
        'evenement',
        'communication_officielle',
    ];

    protected $fillable = [
        'page_type',
        'page_id',
        'auteur_id',
        'type',
        'titre',
        'contenu',
        'image_url',
        'statut',
        'notifier_abonnes',
        'publie_at',
    ];

    protected $casts = [
        'notifier_abonnes' => 'boolean',
        'publie_at' => 'datetime',
    ];

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'auteur_id');
    }

    // Seulement les publications visibles par le public
    public function scopePubliee($query)
    {
        return $query->where('statut', self::STATUT_PUBLIEE)
            ->whereNotNull('publie_at')
            ->where('publie_at', '<=', now());
    }

    // Retrouve la page émettrice (établissement ou club)
    public function page(): ?Model
    {
        return match ($this->page_type) {
            Abonnement::TYPE_ETABLISSEMENT => Etablissement::find($this->page_id),
            Abonnement::TYPE_CLUB => Club::find($this->page_id),
            default => null,
        };
    }
}