<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('reference_sku', 100);
            $table->decimal('prix_unitaire', 15, 2);
            $table->integer('stock_actuel')->default(0);
            $table->integer('stock_minimum')->default(5);
            $table->string('categorie', 100)->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->unique(['entreprise_id', 'reference_sku']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
