<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropUnique('devis_numero_unique');
            $table->unique(['entreprise_id', 'numero'], 'devis_entreprise_numero_unique');
        });
    }

    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropUnique('devis_entreprise_numero_unique');
            $table->unique('numero', 'devis_numero_unique');
        });
    }
};
