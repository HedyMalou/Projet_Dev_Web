<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('COMMENTAIRE', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_candidature')
                  ->constrained('CANDIDATURE')
                  ->onDelete('cascade');
            $table->foreignId('id_utilisateur')
                  ->constrained('UTILISATEUR')
                  ->onDelete('cascade');
            $table->text('contenu');
            $table->timestamp('date')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('COMMENTAIRE');
    }
};
