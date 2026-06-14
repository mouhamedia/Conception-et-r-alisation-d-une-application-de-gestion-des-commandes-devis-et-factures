<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes_devis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_source_id')->constrained('entreprises')->cascadeOnDelete();
            $table->foreignId('entreprise_cible_id')->constrained('entreprises')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->decimal('budget', 15, 2)->nullable();
            $table->enum('statut', ['en_attente', 'acceptee', 'refusee', 'devis_cree'])->default('en_attente');
            $table->foreignId('devis_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes_devis');
    }
};
