<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('AFFECTATION_JURY', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_jury')
                  ->constrained('JURY')
                  ->onDelete('cascade');
            $table->foreignId('id_candidature')
                  ->constrained('CANDIDATURE')
                  ->onDelete('cascade');
            $table->timestamp('date_affectation')->useCurrent();

            $table->unique(['id_jury', 'id_candidature']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('AFFECTATION_JURY');
    }
};
