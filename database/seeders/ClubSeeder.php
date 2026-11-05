<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Etablissement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ClubSeeder extends Seeder
{
    public function run(): void
    {
        $modeles = [
            ['nom' => 'Club Informatique', 'categorie' => 'technologie'],
            ['nom' => 'Club Entrepreneuriat', 'categorie' => 'entrepreneuriat'],
            ['nom' => 'Club Débat', 'categorie' => 'culture'],
        ];

        // 3 clubs officiels pour chacun des 2 premiers établissements
        foreach (Etablissement::orderBy('id')->take(2)->get() as $etablissement) {
            foreach ($modeles as $modele) {
                $nom = $modele['nom'] . ' ' . ($etablissement->sigle ?: $etablissement->nom);

                Club::updateOrCreate(
                    ['slug' => Str::slug($nom)],
                    [
                        'etablissement_id' => $etablissement->id,
                        'nom' => $nom,
                        'description' => 'DONNÉE DE DÉMONSTRATION FICTIVE : à remplacer par les vraies informations.',
                        'categorie' => $modele['categorie'],
                        'est_officiel' => true,
                        'statut_page' => 'non_verifiee',
                        'est_actif' => true,
                    ]
                );
            }
        }

        // 1 club communautaire, sans établissement
        Club::updateOrCreate(
            ['slug' => 'club-photo-communautaire'],
            [
                'etablissement_id' => null,
                'nom' => 'Club Photo Communautaire',
                'description' => 'DONNÉE DE DÉMONSTRATION FICTIVE : club créé par des étudiants.',
                'categorie' => 'culture',
                'est_officiel' => false,
                'statut_page' => 'non_verifiee',
                'est_actif' => true,
            ]
        );
    }
}