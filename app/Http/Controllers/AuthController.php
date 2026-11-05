<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // ÉTAPE 1 : inscription (crée seulement le compte)
    public function inscription(Request $request)
    {
        $request->merge(['email' => strtolower((string) $request->input('email'))]);

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:utilisateurs,email'],
            'telephone' => ['nullable', 'string', 'max:20'],
        ], [
            'email.unique' => 'Cet email est déjà inscrit. Connectez-vous.',
        ]);

        Utilisateur::create($data);

        return response()->json([
            'message' => 'Compte créé. Vous pouvez maintenant vous connecter.',
        ], 201);
    }

    // ÉTAPE 2 : connexion (envoie le code par email)
    public function envoyerCode(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower($data['email']);

        $utilisateur = Utilisateur::where('email', $email)
            ->where('est_actif', true)
            ->first();

        // On n'envoie un code que si le compte existe
        if ($utilisateur) {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // On supprime les anciens codes non utilisés de cet email
            OtpCode::where('email', $email)->whereNull('utilise_at')->delete();

            OtpCode::create([
                'email' => $email,
                'code_hash' => Hash::make($code),
                'code_dev' => app()->environment('local') ? $code : null,
                'expire_at' => now()->addMinutes(10),
            ]);

            Mail::raw(
                "Votre code GOULCOMMUNITY : $code\nIl expire dans 10 minutes.",
                fn ($message) => $message
                    ->to($email)
                    ->subject('Votre code de connexion GOULCOMMUNITY')
            );
        }

        // Même réponse dans tous les cas
        return response()->json([
            'message' => "Si un compte existe avec cet email, un code vient d'être envoyé.",
        ]);
    }

    // ÉTAPE 3 : vérification du code et connexion
    public function verifierCode(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $email = strtolower($data['email']);

        $otp = OtpCode::where('email', $email)
            ->whereNull('utilise_at')
            ->latest()
            ->first();

        if (! $otp || $otp->estExpire() || $otp->tropDeTentatives()) {
            return response()->json(['message' => 'Code invalide ou expiré.'], 422);
        }

        if (! Hash::check($data['code'], $otp->code_hash)) {
            $otp->increment('tentatives');
            return response()->json(['message' => 'Code invalide ou expiré.'], 422);
        }

        $utilisateur = Utilisateur::where('email', $email)
            ->where('est_actif', true)
            ->first();

        if (! $utilisateur) {
            return response()->json(['message' => 'Code invalide ou expiré.'], 422);
        }

        // Le code est bon : on le marque comme utilisé
        $otp->update(['utilise_at' => now()]);

        if (! $utilisateur->email_verifie_at) {
            $utilisateur->update(['email_verifie_at' => now()]);
        }

        // Jeton de connexion pour le frontend React
        $token = $utilisateur->createToken('goulcommunity')->plainTextToken;

        return response()->json([
            'token' => $token,
            'utilisateur' => $utilisateur,
        ]);
    }

    // OUTIL DE DÉVELOPPEMENT : récupérer le dernier code (local seulement)
    public function codeDev(Request $request)
    {
        abort_unless(app()->environment('local'), 404);

        $email = strtolower((string) $request->query('email'));

        $otp = OtpCode::where('email', $email)
            ->whereNull('utilise_at')
            ->latest()
            ->first();

        if (! $otp || ! $otp->code_dev) {
            return response()->json(['message' => 'Aucun code trouvé.'], 404);
        }

        return response()->json([
            'email' => $email,
            'code' => $otp->code_dev,
            'expire_at' => $otp->expire_at,
        ]);
    }
}