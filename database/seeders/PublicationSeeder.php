<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Etablissement;
use App\Models\Publication;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;

class PublicationSeeder extends Seeder
{
    public function run(): void
    {
        $auteurId = Utilisateur::orderBy('id')->value('id');
        $fictif = 'DONNÉE DE DÉMONSTRATION FICTIVE : à remplacer par un vrai contenu.';

        // Publications des 2 premiers établissements
        foreach (Etablissement::orderBy('id')->take(2)->get() as $i => $etablissement) {
            Publication::updateOrCreate(
                ['page_type' => 'etablissement', 'page_id' => $etablissement->id, 'titre' => '[DÉMO] Rentrée académique'],
                [
                    'auteur_id' => $auteurId,
                    'type' => 'annonce',
                    'contenu' => $fictif,
                    'statut' => 'publiee',
                    'notifier_abonnes' => true,
                    'publie_at' => now()->subDays(3 + $i),
                ]
            );

            Publication::updateOrCreate(
                ['page_type' => 'etablissement', 'page_id' => $etablissement->id, 'titre' => '[DÉMO] Journée portes ouvertes'],
                [
                    'auteur_id' => $auteurId,
                    'type' => 'evenement',
                    'contenu' => $fictif,
                    'statut' => 'publiee',
                    'notifier_abonnes' => false,
                    'publie_at' => now()->subDay(),
                ]
            );
        }

        // Un brouillon : il ne doit JAMAIS apparaître dans le fil
        $premier = Etablissement::orderBy('id')->first();
        if ($premier) {
            Publication::updateOrCreate(
                ['page_type' => 'etablissement', 'page_id' => $premier->id, 'titre' => '[DÉMO] Brouillon non publié'],
                [
                    'auteur_id' => $auteurId,
                    'type' => 'actualite',
                    'contenu' => $fictif,
                    'statut' => 'brouillon',
                    'notifier_abonnes' => false,
                    'publie_at' => null,
                ]
            );
        }

        // Publications de 4 clubs officiels
        foreach (Club::where('est_officiel', true)->orderBy('id')->take(4)->get() as $i => $club) {
            Publication::updateOrCreate(
                ['page_type' => 'club', 'page_id' => $club->id, 'titre' => '[DÉMO] Première réunion du club'],
                [
                    'auteur_id' => $auteurId,
                    'type' => 'actualite',
                    'contenu' => $fictif,
                    'statut' => 'publiee',
                    'notifier_abonnes' => true,
                    'publie_at' => now()->subHours(5 + $i),
                ]
            );
        }
    }
}