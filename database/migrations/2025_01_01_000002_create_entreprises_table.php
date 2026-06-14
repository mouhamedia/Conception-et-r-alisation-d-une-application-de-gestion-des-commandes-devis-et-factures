<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entreprises', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('siret', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('pays', 100)->default('Sénégal');
            $table->string('logo')->nullable();
            $table->string('devise', 10)->default('FCFA');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entreprises');
    }
};
