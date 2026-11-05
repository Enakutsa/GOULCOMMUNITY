<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Club;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AbonnementController extends Controller
{
    // Mes abonnements
    public function index(Request $request)
    {
        $abonnements = Abonnement::where('utilisateur_id', $request->user()->id)->get();

        $etablissementIds = $abonnements->where('page_type', Abonnement::TYPE_ETABLISSEMENT)->pluck('page_id');
        $clubIds = $abonnements->where('page_type', Abonnement::TYPE_CLUB)->pluck('page_id');

        return response()->json([
            'etablissements' => Etablissement::publie()
                ->whereIn('id', $etablissementIds)
                ->get(['id', 'nom', 'sigle', 'slug', 'ville', 'logo_url']),
            'clubs' => Club::where('est_actif', true)
                ->whereIn('id', $clubIds)
                ->get(['id', 'nom', 'slug', 'etablissement_id', 'logo_url']),
        ]);
    }

    // Suivre une page
    public function store(Request $request)
    {
        $data = $request->validate([
            'page_type' => ['required', Rule::in([Abonnement::TYPE_ETABLISSEMENT, Abonnement::TYPE_CLUB])],
            'page_id' => ['required', 'integer'],
        ]);

        if (! $this->pageExiste($data['page_type'], $data['page_id'])) {
            return response()->json(['message' => 'Page introuvable.'], 404);
        }

        Abonnement::firstOrCreate([
            'utilisateur_id' => $request->user()->id,
            'page_type' => $data['page_type'],
            'page_id' => $data['page_id'],
        ]);

        return response()->json(['message' => 'Abonnement enregistré.'], 201);
    }

    // Ne plus suivre une page
    public function destroy(Request $request, string $type, int $id)
    {
        Abonnement::where('utilisateur_id', $request->user()->id)
            ->where('page_type', $type)
            ->where('page_id', $id)
            ->delete();

        return response()->json(['message' => 'Abonnement supprimé.']);
    }

    private function pageExiste(string $type, int $id): bool
    {
        if ($type === Abonnement::TYPE_ETABLISSEMENT) {
            return Etablissement::publie()->whereKey($id)->exists();
        }

        return Club::where('est_actif', true)->whereKey($id)->exists();
    }
}