<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commande_id')->constrained()->cascadeOnDelete();
            $table->string('numero', 20)->unique();
            $table->enum('statut', ['brouillon', 'envoyee', 'payee', 'en_retard'])->default('brouillon');
            $table->decimal('montant_paye', 15, 2)->default(0);
            $table->date('date_echeance');
            $table->timestamp('payee_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
