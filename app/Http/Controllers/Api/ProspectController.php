<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProspectController extends Controller
{
    private const MESSAGE = 'Merci ! Votre demande est bien enregistrée.';

    // Formulaire d'intérêt (public)
    public function store(Request $request)
    {
        // Piège anti-robots : ce champ est caché, un humain ne le remplit pas
        if ($request->filled('site_web_confirmation')) {
            return response()->json(['message' => self::MESSAGE], 201);
        }

        $request->merge(['email' => strtolower((string) $request->input('email'))]);

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'profil' => ['required', Rule::in(['etudiant', 'bachelier', 'etablissement', 'club', 'autre'])],
            'etablissement_interesse' => ['nullable', 'string', 'max:200'],
            // Obligatoire : sans cet accord, on n'enregistre rien
            'consentement_information' => ['accepted'],
            // Facultatif et séparé
            'consentement_promotionnel' => ['nullable', 'boolean'],
        ], [
            'consentement_information.accepted' => "Vous devez accepter que vos informations soient utilisées pour vous répondre.",
        ]);

        // Si l'email existe déjà, on ne modifie rien : même réponse dans tous les cas
        $existe = Prospect::where('email', $data['email'])->exists();

        if (! $existe) {
            Prospect::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'] ?? null,
                'email' => $data['email'],
                'telephone' => $data['telephone'] ?? null,
                'profil' => $data['profil'],
                'etablissement_interesse' => $data['etablissement_interesse'] ?? null,
                'consentement_information' => true,
                'consentement_promotionnel' => (bool) ($data['consentement_promotionnel'] ?? false),
                'consentement_at' => now(),
                'token_desabonnement' => Str::random(64),
            ]);
        }

        return response()->json(['message' => self::MESSAGE], 201);
    }

    // Désabonnement des communications (lien reçu par email)
    public function desabonner(string $token)
    {
        $prospect = Prospect::where('token_desabonnement', $token)->first();

        if (! $prospect) {
            return response()->json(['message' => 'Lien invalide.'], 404);
        }

        if ($prospect->desabonne_at === null) {
            $prospect->update([
                'consentement_promotionnel' => false,
                'desabonne_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Vous êtes désabonné des communications.']);
    }
}