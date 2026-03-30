<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('UTILISATEUR', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('email', 150)->unique();
            $table->string('mot_de_passe', 255);
            $table->enum('role', ['etudiant', 'tuteur', 'jury', 'entreprise', 'admin']);
            $table->timestamp('created_at')->useCurrent();
            // Pas de updated_at dans le schéma d'origine
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('UTILISATEUR');
    }
};
