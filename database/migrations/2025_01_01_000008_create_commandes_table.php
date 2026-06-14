<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->constrained()->cascadeOnDelete();
            $table->foreignId('devis_id')->nullable()->constrained()->nullOnDelete();
            $table->string('numero', 20)->unique();
            $table->string('client_nom');
            $table->enum('statut', ['en_attente', 'en_cours', 'livree', 'annulee'])->default('en_attente');
            $table->decimal('sous_total_ht', 15, 2)->default(0);
            $table->decimal('tva', 5, 2)->default(18.00);
            $table->decimal('total_ttc', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
