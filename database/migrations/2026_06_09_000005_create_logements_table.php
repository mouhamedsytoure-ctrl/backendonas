<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('immeuble_id')->constrained('immeubles')->cascadeOnDelete();
            $table->string('reference');              // ex : A1
            $table->integer('etage')->default(0);     // 0 = rez-de-chaussee
            $table->enum('type', ['appartement', 'studio', 'mini_studio', 'local_commercial'])
                  ->default('appartement');
            $table->decimal('loyer', 12, 2)->default(0);
            $table->enum('statut', ['disponible', 'loue', 'indisponible'])->default('disponible');
            $table->integer('nb_pieces')->nullable();
            $table->decimal('surface', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Le nom est unique PAR ETAGE -> A1 possible a chaque etage,
            // mais pas deux A1 sur le meme etage.
            $table->unique(['immeuble_id', 'etage', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logements');
    }
};