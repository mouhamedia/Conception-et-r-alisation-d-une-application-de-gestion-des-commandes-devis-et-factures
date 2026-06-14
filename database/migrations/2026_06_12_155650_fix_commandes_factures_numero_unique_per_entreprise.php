<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropUnique('commandes_numero_unique');
            $table->unique(['entreprise_id', 'numero'], 'commandes_entreprise_numero_unique');
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->dropUnique('factures_numero_unique');
            $table->unique(['entreprise_id', 'numero'], 'factures_entreprise_numero_unique');
        });
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropUnique('commandes_entreprise_numero_unique');
            $table->unique('numero', 'commandes_numero_unique');
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->dropUnique('factures_entreprise_numero_unique');
            $table->unique('numero', 'factures_numero_unique');
        });
    }
};
