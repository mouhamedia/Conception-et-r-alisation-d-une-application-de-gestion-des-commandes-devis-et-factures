<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('numero', 20)->unique();
            $table->string('client_nom');
            $table->string('client_email')->nullable();
            $table->string('client_telephone', 20)->nullable();
            $table->string('client_adresse')->nullable();
            $table->enum('statut', ['brouillon', 'envoye', 'accepte', 'refuse', 'expire'])->default('brouillon');
            $table->decimal('sous_total_ht', 15, 2)->default(0);
            $table->decimal('tva', 5, 2)->default(18.00);
            $table->decimal('total_ttc', 15, 2)->default(0);
            $table->date('date_emission');
            $table->date('date_expiration');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
