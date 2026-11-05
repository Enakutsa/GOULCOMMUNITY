<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClubController extends Controller
{
    // Clubs visibles publiquement : actifs et ni suspendus ni rejetés
    private function visibles()
    {
        return Club::with('etablissement:id,nom,sigle,slug')
            ->where('est_actif', true)
            ->whereNotIn('statut_page', ['suspendue', 'rejetee']);
    }

    // Liste des clubs, avec recherche et filtres
    public function index(Request $request)
    {
        $query = $this->visibles();

        if ($request->filled('q')) {
            $query->where('nom', 'ilike', '%' . $request->q . '%');
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('etablissement')) {
            $query->whereHas('etablissement', function ($q) use ($request) {
                $q->where('slug', $request->etablissement);
            });
        }

        if ($request->has('officiel')) {
            $query->where('est_officiel', $request->boolean('officiel'));
        }

        return response()->json($query->orderBy('nom')->paginate(12));
    }

    // Fiche d'un club
    public function show(string $slug)
    {
        $club = $this->visibles()->where('slug', $slug)->firstOrFail();

        return response()->json(['data' => $club]);
    }

    // Création d'un club par un utilisateur connecté
    public function store(Request $request)
    {
        $utilisateur = $request->user();

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'categorie' => ['nullable', 'string', 'max:60'],
            'etablissement_id' => ['nullable', 'integer'],
        ]);

        // Limite anti-abus : 5 clubs par utilisateur
        if (Club::where('createur_id', $utilisateur->id)->count() >= 5) {
            return response()->json(['message' => 'Vous avez atteint la limite de 5 clubs créés.'], 422);
        }

        // L'établissement indiqué doit exister et être publié
        if (! empty($data['etablissement_id'])
            && ! Etablissement::publie()->whereKey($data['etablissement_id'])->exists()) {
            return response()->json(['message' => 'Établissement introuvable.'], 422);
        }

        $club = Club::create([
            'etablissement_id' => $data['etablissement_id'] ?? null,
            'createur_id' => $utilisateur->id,
            'nom' => $data['nom'],
            'slug' => $this->slugUnique($data['nom']),
            'description' => $data['description'] ?? null,
            'categorie' => $data['categorie'] ?? null,
            // Un club créé par un utilisateur n'est JAMAIS officiel d'office
            'est_officiel' => false,
            'statut_page' => 'non_verifiee',
            'est_actif' => true,
        ]);

        return response()->json(['data' => $club], 201);
    }

    private function slugUnique(string $nom): string
    {
        $base = Str::slug($nom) ?: 'club';
        $slug = $base;
        $i = 2;

        while (Club::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}