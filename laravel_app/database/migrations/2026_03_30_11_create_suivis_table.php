<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('SUIVI', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tuteur')
                  ->constrained('TUTEUR')
                  ->onDelete('cascade');
            $table->foreignId('id_candidature')
                  ->unique()
                  ->constrained('CANDIDATURE')
                  ->onDelete('cascade');
            $table->decimal('note_finale', 4, 2)->nullable();
            $table->date('date_debut')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('SUIVI');
    }
};
