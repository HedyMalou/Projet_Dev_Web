<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('DOCUMENT', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_candidature')
                  ->constrained('CANDIDATURE')
                  ->onDelete('cascade');
            $table->enum('type', ['rapport', 'resume', 'fiche_evaluation', 'convention', 'autre']);
            $table->string('chemin_fichier', 255);
            $table->timestamp('date_depot')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DOCUMENT');
    }
};
