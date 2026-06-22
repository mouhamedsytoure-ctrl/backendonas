<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();       // le locataire
            $table->foreignId('logement_id')->nullable()
                  ->constrained('logements')->nullOnDelete();
            $table->string('objet');
            $table->text('description')->nullable();
            $table->enum('priorite', ['basse', 'normale', 'haute'])->default('normale');
            $table->enum('statut', ['ouvert', 'en_cours', 'resolu'])->default('ouvert');
            $table->boolean('escalade_super_admin')->default(false);
            $table->foreignId('traite_par')->nullable()
                  ->constrained('users')->nullOnDelete();                                 // l'admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamations');
    }
};
