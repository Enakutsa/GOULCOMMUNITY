<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Club;
use App\Models\Etablissement;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    // Notifications visibles : celles dont la publication est toujours publiée
    private function visibles(int $utilisateurId)
    {
        return Notification::where('utilisateur_id', $utilisateurId)
            ->whereHas('publication', fn ($q) => $q->publiee());
    }

    // Liste des notifications (historique). ?non_lues=1 pour ne voir que les non lues
    public function index(Request $request)
    {
        $query = $this->visibles($request->user()->id)
            ->with('publication:id,page_type,page_id,titre,type,publie_at');

        if ($request->boolean('non_lues')) {
            $query->whereNull('lu_at');
        }

        $notifications = $query->orderByDesc('created_at')->orderByDesc('id')->paginate(20);
        $items = $notifications->getCollection();

        $publications = $items->pluck('publication')->filter();

        $etablissements = Etablissement::whereIn('id', $publications->where('page_type', Abonnement::TYPE_ETABLISSEMENT)->pluck('page_id'))
            ->get(['id', 'nom', 'sigle', 'slug', 'logo_url'])
            ->keyBy('id');

        $clubs = Club::whereIn('id', $publications->where('page_type', Abonnement::TYPE_CLUB)->pluck('page_id'))
            ->get(['id', 'nom', 'slug', 'logo_url'])
            ->keyBy('id');

        $notifications->setCollection($items->map(function ($n) use ($etablissements, $clubs) {
            $p = $n->publication;

            $page = null;
            if ($p) {
                $page = $p->page_type === Abonnement::TYPE_ETABLISSEMENT
                    ? $etablissements->get($p->page_id)
                    : $clubs->get($p->page_id);
            }

            return [
                'id' => $n->id,
                'lu' => $n->lu_at !== null,
                'lu_at' => $n->lu_at,
                'recue_at' => $n->created_at,
                'publication' => $p ? [
                    'id' => $p->id,
                    'titre' => $p->titre,
                    'type' => $p->type,
                ] : null,
                'page' => $page ? [
                    'type' => $p->page_type,
                    'id' => $page->id,
                    'nom' => $page->nom,
                    'slug' => $page->slug,
                    'logo_url' => $page->logo_url,
                ] : null,
            ];
        }));

        return response()->json($notifications);
    }

    // Compteur de notifications non lues (pour l'icône de la barre de navigation)
    public function compteur(Request $request)
    {
        $nombre = $this->visibles($request->user()->id)->whereNull('lu_at')->count();

        return response()->json(['non_lues' => $nombre]);
    }

    // Marquer une notification comme lue (et la publication aussi)
    public function marquerLue(Request $request, int $id)
    {
        $utilisateur = $request->user();

        // Seulement SES propres notifications
        $notification = Notification::where('utilisateur_id', $utilisateur->id)
            ->whereKey($id)
            ->firstOrFail();

        if ($notification->lu_at === null) {
            $notification->update(['lu_at' => now()]);
        }

        DB::table('lectures_publications')->insertOrIgnore([
            'utilisateur_id' => $utilisateur->id,
            'publication_id' => $notification->publication_id,
        ]);

        return response()->json(['message' => 'Notification marquée comme lue.']);
    }

    // Tout marquer comme lu
    public function toutLu(Request $request)
    {
        $utilisateur = $request->user();

        $publicationIds = Notification::where('utilisateur_id', $utilisateur->id)
            ->whereNull('lu_at')
            ->pluck('publication_id');

        DB::transaction(function () use ($utilisateur, $publicationIds) {
            Notification::where('utilisateur_id', $utilisateur->id)
                ->whereNull('lu_at')
                ->update(['lu_at' => now()]);

            foreach ($publicationIds->chunk(500) as $paquet) {
                DB::table('lectures_publications')->insertOrIgnore(
                    $paquet->map(fn ($pid) => [
                        'utilisateur_id' => $utilisateur->id,
                        'publication_id' => $pid,
                    ])->all()
                );
            }
        });

        return response()->json(['message' => 'Toutes les notifications sont marquées comme lues.']);
    }
}