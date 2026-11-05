<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lectures_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->cascadeOnDelete();
            $table->foreignId('publication_id')->constrained('publications')->cascadeOnDelete();
            $table->timestamp('lu_at')->useCurrent();

            // Un utilisateur ne peut avoir qu'une seule lecture par publication
            $table->unique(['utilisateur_id', 'publication_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lectures_publications');
    }
};