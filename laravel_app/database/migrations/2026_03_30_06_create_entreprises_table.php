<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ENTREPRISE', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_utilisateur')
                  ->unique()
                  ->constrained('UTILISATEUR')
                  ->onDelete('cascade');
            $table->string('nom_entreprise', 150);
            $table->string('secteur', 100)->nullable();
            $table->string('adresse', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ENTREPRISE');
    }
};
