<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abonnements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->cascadeOnDelete();
            $table->string('page_type'); // 'etablissement' ou 'club'
            $table->unsignedBigInteger('page_id');
            $table->timestamps();

            // Un utilisateur ne peut pas suivre deux fois la même page
            $table->unique(['utilisateur_id', 'page_type', 'page_id']);
            // Pour retrouver vite tous les abonnés d'une page
            $table->index(['page_type', 'page_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abonnements');
    }
};