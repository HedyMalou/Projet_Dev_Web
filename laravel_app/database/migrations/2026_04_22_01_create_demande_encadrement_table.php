<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('DEMANDE_ENCADREMENT', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_utilisateur')
                  ->constrained('UTILISATEUR')
                  ->onDelete('cascade');
            $table->string('nom_etudiant', 100);
            $table->string('prenom_etudiant', 100);
            $table->string('numero_etudiant', 50);
            $table->foreignId('id_candidature')
                  ->nullable()
                  ->constrained('CANDIDATURE')
                  ->onDelete('set null');
            $table->enum('statut', ['en_attente', 'acceptee', 'refusee'])->default('en_attente');
            $table->string('motif_refus', 500)->nullable();
            $table->timestamp('date_demande')->useCurrent();
            $table->timestamp('date_traitement')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DEMANDE_ENCADREMENT');
    }
};
