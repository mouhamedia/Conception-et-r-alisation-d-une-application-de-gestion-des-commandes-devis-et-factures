<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ligne_commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained()->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained()->restrictOnDelete();
            $table->integer('quantite');
            $table->decimal('prix_unitaire_snapshot', 15, 2);
            $table->decimal('sous_total', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_commandes');
    }
};
