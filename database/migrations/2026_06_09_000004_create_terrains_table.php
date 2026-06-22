<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terrains', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('ville')->nullable();
            $table->string('surface')->nullable();
            $table->enum('statut', ['disponible', 'constructible', 'en_projet', 'vendu'])
                  ->default('disponible');
            $table->enum('type_document', ['titre_foncier', 'bail', 'autre'])->nullable();
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terrains');
    }
};
