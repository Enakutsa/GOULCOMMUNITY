<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Abonnement extends Model
{
    protected $table = 'abonnements';

    public const TYPE_ETABLISSEMENT = 'etablissement';
    public const TYPE_CLUB = 'club';

    protected $fillable = [
        'utilisateur_id',
        'page_type',
        'page_id',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class);
    }

    // Retrouve la page suivie (un établissement ou un club)
    public function page(): ?Model
    {
        return match ($this->page_type) {
            self::TYPE_ETABLISSEMENT => Etablissement::find($this->page_id),
            self::TYPE_CLUB => Club::find($this->page_id),
            default => null,
        };
    }
}