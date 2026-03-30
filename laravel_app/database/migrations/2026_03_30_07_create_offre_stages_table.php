<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('OFFRE_STAGE', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_entreprise')
                  ->constrained('ENTREPRISE')
                  ->onDelete('cascade');
            $table->string('titre', 200);
            $table->text('description')->nullable();
            $table->text('competences')->nullable();
            $table->string('duree', 50)->nullable();
            $table->string('lieu', 150)->nullable();
            $table->timestamp('date_publication')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('OFFRE_STAGE');
    }
};
