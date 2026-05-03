<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('USER_ACTIVITE', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_utilisateur')
                  ->constrained('UTILISATEUR')
                  ->onDelete('cascade');
            $table->enum('type', ['acces', 'action']);
            $table->string('detail', 255)->nullable();
            $table->timestamp('date_action')->useCurrent();
            $table->index(['id_utilisateur', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('USER_ACTIVITE');
    }
};
