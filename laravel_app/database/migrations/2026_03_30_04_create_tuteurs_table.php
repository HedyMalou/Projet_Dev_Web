<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TUTEUR', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_utilisateur')
                  ->unique()
                  ->constrained('UTILISATEUR')
                  ->onDelete('cascade');
            $table->string('departement', 100);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TUTEUR');
    }
};
