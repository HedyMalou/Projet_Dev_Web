<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE DOCUMENT MODIFY type ENUM('cv','lettre_motivation','rapport','resume','fiche_evaluation','convention','autre') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE DOCUMENT MODIFY type ENUM('rapport','resume','fiche_evaluation','convention','autre') NOT NULL");
    }
};
