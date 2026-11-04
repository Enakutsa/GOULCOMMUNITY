<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EtablissementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Etablissement::publie()->withCount('formations');

        if ($q = trim((string) $request->query('q'))) {
            $like = '%' . $q . '%';
            $query->where(function ($sub) use ($like) {
                $sub->where('nom', 'ilike', $like)
                    ->orWhere('sigle', 'ilike', $like)
                    ->orWhere('description', 'ilike', $like);
            });
        }

        if ($ville = $request->query('ville')) {
            $query->where('ville', $ville);
        }

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($secteur = $request->query('secteur')) {
            $query->where('secteur', $secteur);
        }

        return response()->json(
            $query->orderBy('nom')->paginate(12)
        );
    }

    public function show(string $slug): JsonResponse
    {
        $etablissement = Etablissement::publie()
            ->with('formations:id,etablissement_id,intitule,niveau,description')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($etablissement);
    }
}