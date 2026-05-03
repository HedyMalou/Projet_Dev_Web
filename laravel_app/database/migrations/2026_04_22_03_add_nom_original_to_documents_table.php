<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('DOCUMENT', function (Blueprint $table) {
            $table->string('nom_original', 255)->nullable()->after('chemin_fichier');
        });
    }

    public function down(): void
    {
        Schema::table('DOCUMENT', function (Blueprint $table) {
            $table->dropColumn('nom_original');
        });
    }
};
