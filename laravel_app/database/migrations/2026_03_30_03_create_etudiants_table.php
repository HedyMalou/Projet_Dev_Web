<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ETUDIANT', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_utilisateur')
                  ->unique()
                  ->constrained('UTILISATEUR')
                  ->onDelete('cascade');
            $table->string('filiere', 100);
            $table->string('promotion', 10);
            $table->string('numero_etudiant', 20)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ETUDIANT');
    }
};
