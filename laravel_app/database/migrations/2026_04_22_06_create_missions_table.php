<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('MISSION', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_candidature')
                  ->constrained('CANDIDATURE')
                  ->onDelete('cascade');
            $table->string('titre', 200);
            $table->text('description')->nullable();
            $table->enum('statut', ['en_cours', 'terminee'])->default('en_cours');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('MISSION');
    }
};
