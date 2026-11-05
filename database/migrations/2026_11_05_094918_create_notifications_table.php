<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->cascadeOnDelete();
            $table->foreignId('publication_id')->constrained('publications')->cascadeOnDelete();
            $table->timestamp('lu_at')->nullable();
            $table->timestamps();

            // Évite les notifications en double pour un même utilisateur et un même contenu
            $table->unique(['utilisateur_id', 'publication_id']);
            // Pour compter vite les notifications non lues d'un utilisateur
            $table->index(['utilisateur_id', 'lu_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};