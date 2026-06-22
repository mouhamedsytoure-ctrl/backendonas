<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrat_id')->constrained('contrats')->cascadeOnDelete();
            $table->string('periode');                 // ex : 2026-06
            $table->decimal('montant', 12, 2);
            $table->enum('mode_paiement', ['wave', 'orange_money', 'especes'])->nullable();
            $table->enum('statut', ['paye', 'en_attente', 'retard', 'impaye'])->default('en_attente');
            $table->dateTime('date_paiement')->nullable();
            $table->string('reference_transaction')->nullable(); // pour Wave / Orange Money plus tard
            $table->string('recu_numero')->nullable()->unique();  // genere quand statut = paye
            $table->string('recu_fichier')->nullable();           // PDF du recu
            $table->foreignId('enregistre_par')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['contrat_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
