<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Club;
use App\Models\Etablissement;
use App\Models\LecturePublication;
use App\Models\Publication;
use Illuminate\Http\Request;

class FilActualiteController extends Controller
{
    // Fil d'actualité : publications des pages auxquelles l'utilisateur est abonné
    public function index(Request $request)
    {
        $utilisateur = $request->user();

        $abonnements = Abonnement::where('utilisateur_id', $utilisateur->id)
            ->get(['page_type', 'page_id']);

        $etablissementIds = $abonnements->where('page_type', Abonnement::TYPE_ETABLISSEMENT)->pluck('page_id');
        $clubIds = $abonnements->where('page_type', Abonnement::TYPE_CLUB)->pluck('page_id');

        $publications = Publication::publiee()
            ->where(function ($q) use ($etablissementIds, $clubIds) {
                $q->where(function ($w) use ($etablissementIds) {
                    $w->where('page_type', Abonnement::TYPE_ETABLISSEMENT)
                        ->whereIn('page_id', $etablissementIds);
                })->orWhere(function ($w) use ($clubIds) {
                    $w->where('page_type', Abonnement::TYPE_CLUB)
                        ->whereIn('page_id', $clubIds);
                });
            })
            ->with('auteur:id,nom,prenom')
            ->orderByDesc('publie_at')
            ->paginate(15);

        $items = $publications->getCollection();

        // Publications déjà lues par CET utilisateur uniquement
        $luesIds = LecturePublication::where('utilisateur_id', $utilisateur->id)
            ->whereIn('publication_id', $items->pluck('id'))
            ->pluck('publication_id');

        // On charge les pages émettrices en une seule fois
        $etablissements = Etablissement::whereIn('id', $items->where('page_type', Abonnement::TYPE_ETABLISSEMENT)->pluck('page_id'))
            ->get(['id', 'nom', 'sigle', 'slug', 'logo_url'])
            ->keyBy('id');

        $clubs = Club::whereIn('id', $items->where('page_type', Abonnement::TYPE_CLUB)->pluck('page_id'))
            ->get(['id', 'nom', 'slug', 'logo_url'])
            ->keyBy('id');

        $publications->setCollection($items->map(function ($p) use ($etablissements, $clubs, $luesIds) {
            $page = $p->page_type === Abonnement::TYPE_ETABLISSEMENT
                ? $etablissements->get($p->page_id)
                : $clubs->get($p->page_id);

            return [
                'id' => $p->id,
                'type' => $p->type,
                'titre' => $p->titre,
                'contenu' => $p->contenu,
                'image_url' => $p->image_url,
                'publie_at' => $p->publie_at,
                'lu' => $luesIds->contains($p->id),
                'auteur' => $p->auteur ? trim($p->auteur->prenom . ' ' . $p->auteur->nom) : null,
                'page' => $page ? [
                    'type' => $p->page_type,
                    'id' => $page->id,
                    'nom' => $page->nom,
                    'slug' => $page->slug,
                    'logo_url' => $page->logo_url,
                ] : null,
            ];
        }));

        return response()->json($publications);
    }

    // Marquer une publication comme lue (pour cet utilisateur seulement)
    public function marquerLu(Request $request, int $id)
    {
        $utilisateur = $request->user();

        $publication = Publication::publiee()->whereKey($id)->first();

        // La publication doit exister et l'utilisateur doit suivre sa page
        $abonne = $publication && Abonnement::where('utilisateur_id', $utilisateur->id)
            ->where('page_type', $publication->page_type)
            ->where('page_id', $publication->page_id)
            ->exists();

        if (! $abonne) {
            return response()->json(['message' => 'Publication introuvable.'], 404);
        }

        LecturePublication::firstOrCreate([
            'utilisateur_id' => $utilisateur->id,
            'publication_id' => $publication->id,
        ]);

        return response()->json(['message' => 'Publication marquée comme lue.']);
    }
}