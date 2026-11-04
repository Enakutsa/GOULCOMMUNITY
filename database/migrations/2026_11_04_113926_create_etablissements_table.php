<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('type')->default('universite'); // universite, ecole, institut
            $table->text('description')->nullable();
            $table->string('ville')->nullable()->index();
            $table->string('quartier')->nullable();
            $table->string('adresse')->nullable();
            $table->string('site_web')->nullable();
            $table->string('email_contact')->nullable();
            $table->string('telephone')->nullable();
            $table->string('logo_url')->nullable();
            // publiee = ajoutée par l'équipe, revendiquee = demande en cours, verifiee = validée
            $table->string('statut_page')->default('publiee');
            $table->boolean('est_publie')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etablissements');
    }
};