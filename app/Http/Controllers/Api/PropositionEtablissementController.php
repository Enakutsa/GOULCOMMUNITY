<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PropositionEtablissementController extends Controller
{
    public function store(Request $request)
    {
        $utilisateur = $request->user();

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:200'],
            'sigle' => ['nullable', 'string', 'max:30'],
            'type' => ['required', 'string', 'max:60'],
            'secteur' => ['nullable', 'string', 'max:60'],
            'ville' => ['required', 'string', 'max:100'],
            'quartier' => ['nullable', 'string', 'max:100'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'site_web' => ['nullable', 'url', 'max:255'],
            'email_contact' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
        ]);

        // Limite anti-abus : 3 propositions en attente par utilisateur
        $enAttente = Etablissement::where('createur_id', $utilisateur->id)
            ->where('est_publie', false)
            ->count();

        if ($enAttente >= 3) {
            return response()->json([
                'message' => 'Vous avez déjà 3 propositions en attente de validation.',
            ], 422);
        }

        // Évite les doublons
        if (Etablissement::where('nom', 'ilike', $data['nom'])->exists()) {
            return response()->json([
                'message' => "Un établissement portant ce nom existe déjà. Cherchez-le dans l'annuaire.",
            ], 422);
        }

        $etablissement = Etablissement::create($data + [
            'slug' => $this->slugUnique($data['nom']),
            'createur_id' => $utilisateur->id,
            // Cachée au public tant qu'un administrateur ne l'a pas validée
            'est_publie' => false,
            'statut_page' => 'en_verification',
        ]);

        return response()->json([
            'message' => "Proposition enregistrée. L'établissement sera visible après validation par l'équipe.",
            'data' => ['id' => $etablissement->id, 'nom' => $etablissement->nom],
        ], 201);
    }

    private function slugUnique(string $nom): string
    {
        $base = Str::slug($nom) ?: 'etablissement';
        $slug = $base;
        $i = 2;

        while (Etablissement::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}