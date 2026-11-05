<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();

            // Page émettrice : un établissement OU un club
            $table->string('page_type'); // 'etablissement' ou 'club'
            $table->unsignedBigInteger('page_id');

            $table->foreignId('auteur_id')->nullable()->constrained('utilisateurs')->nullOnDelete();

            // actualite, annonce, info_pratique, evenement, communication_officielle
            $table->string('type')->default('actualite');
            $table->string('titre');
            $table->text('contenu');
            $table->string('image_url')->nullable();

            // brouillon, publiee, retiree
            $table->string('statut')->default('brouillon');
            $table->boolean('notifier_abonnes')->default(false);
            $table->timestamp('publie_at')->nullable();

            $table->timestamps();

            $table->index(['page_type', 'page_id', 'statut', 'publie_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};