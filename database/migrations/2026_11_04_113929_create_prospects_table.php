<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            // etudiant, bachelier, etablissement, club, autre
            $table->string('profil')->default('etudiant');
            $table->string('etablissement_interesse')->nullable();
            // Consentements séparés (cahier des charges, section 8)
            $table->boolean('consentement_information')->default(false);
            $table->boolean('consentement_promotionnel')->default(false);
            $table->timestamp('consentement_at')->nullable();
            $table->string('token_desabonnement', 64)->unique();
            $table->timestamp('desabonne_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};