<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crée la table DEMANDE_FORMATION.
     * Permet à un étudiant de demander à l'administrateur l'ajout d'une nouvelle filière
     * lorsque celle-ci n'existe pas encore dans le système.
     */
    public function up(): void
    {
        Schema::create('DEMANDE_FORMATION', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_etudiant')
                  ->constrained('ETUDIANT')
                  ->onDelete('cascade');
            $table->string('nom_formation', 100);
            $table->text('description')->nullable();
            $table->enum('statut', ['en_attente', 'validee', 'refusee'])->default('en_attente');
            $table->text('reponse_admin')->nullable();
            $table->timestamp('date_demande')->useCurrent();
            $table->timestamp('date_traitement')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DEMANDE_FORMATION');
    }
};
