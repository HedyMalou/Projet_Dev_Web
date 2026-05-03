<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('CONVENTION', function (Blueprint $table) {
            $table->string('chemin_fichier', 255)->nullable()->after('id_candidature');
            $table->string('nom_original', 255)->nullable()->after('chemin_fichier');
            $table->enum('statut_admin', ['en_attente', 'signe'])->default('en_attente')->after('statut_tuteur');
        });
    }

    public function down(): void
    {
        Schema::table('CONVENTION', function (Blueprint $table) {
            $table->dropColumn(['chemin_fichier', 'nom_original', 'statut_admin']);
        });
    }
};
