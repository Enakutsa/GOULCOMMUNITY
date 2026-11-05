<?php

namespace App\Services;

use App\Models\Abonnement;
use App\Models\Notification;
use App\Models\Publication;

class DiffusionNotifications
{
    // Crée une notification pour chaque abonné de la page émettrice.
    // Retourne le nombre de notifications créées.
    public function diffuser(Publication $publication): int
    {
        if (! $publication->notifier_abonnes || $publication->statut !== Publication::STATUT_PUBLIEE) {
            return 0;
        }

        $total = 0;

        Abonnement::where('page_type', $publication->page_type)
            ->where('page_id', $publication->page_id)
            // L'auteur ne se notifie pas lui-même
            ->when($publication->auteur_id, fn ($q) => $q->where('utilisateur_id', '!=', $publication->auteur_id))
            ->orderBy('id')
            ->chunkById(500, function ($abonnements) use ($publication, &$total) {
                $maintenant = now();

                $lignes = $abonnements->map(fn ($a) => [
                    'utilisateur_id' => $a->utilisateur_id,
                    'publication_id' => $publication->id,
                    'created_at' => $maintenant,
                    'updated_at' => $maintenant,
                ])->all();

                // insertOrIgnore : pas de doublon si la notification existe déjà
                $total += Notification::insertOrIgnore($lignes);
            });

        return $total;
    }
}