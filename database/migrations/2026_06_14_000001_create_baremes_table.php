<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('baremes', function (Blueprint $t) {
            $t->id();
            $t->string('service');                 // western_union / ria / orange_money
            $t->enum('type', ['envoi', 'retrait']);
            $t->enum('mode', ['percent', 'fixe', 'paliers'])->default('percent');
            $t->decimal('valeur', 12, 2)->nullable(); // % ou montant fixe
            $t->json('paliers')->nullable();          // [{max, comm}, ...]
            $t->timestamps();
            $t->unique(['service', 'type']);
        });
    }
    public function down(): void { Schema::dropIfExists('baremes'); }
};
