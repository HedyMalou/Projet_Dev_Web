<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CANDIDATURE', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_etudiant')
                  ->constrained('ETUDIANT')
                  ->onDelete('cascade');
            $table->foreignId('id_offre')
                  ->constrained('OFFRE_STAGE')
                  ->onDelete('cascade');
            $table->enum('statut', ['en_attente', 'validee', 'refusee', 'archivee'])
                  ->default('en_attente');
            $table->timestamp('date_candidature')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CANDIDATURE');
    }
};
