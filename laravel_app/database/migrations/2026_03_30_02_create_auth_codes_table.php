<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('AUTH_CODE', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_utilisateur')
                  ->constrained('UTILISATEUR')
                  ->onDelete('cascade');
            $table->string('code', 6);
            $table->dateTime('date_expiration');
            $table->tinyInteger('utilise')->default(0);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('AUTH_CODE');
    }
};
