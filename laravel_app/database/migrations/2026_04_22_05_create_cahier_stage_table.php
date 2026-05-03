<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CAHIER_STAGE', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_candidature')
                  ->constrained('CANDIDATURE')
                  ->onDelete('cascade');
            $table->date('date_jour');
            $table->text('contenu');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('CAHIER_STAGE');
    }
};
