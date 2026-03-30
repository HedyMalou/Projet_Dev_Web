<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CONVENTION', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_candidature')
                  ->unique()
                  ->constrained('CANDIDATURE')
                  ->onDelete('cascade');
            $table->enum('statut_etudiant', ['en_attente', 'signe'])->default('en_attente');
            $table->enum('statut_entreprise', ['en_attente', 'signe'])->default('en_attente');
            $table->enum('statut_tuteur', ['en_attente', 'signe'])->default('en_attente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CONVENTION');
    }
};
