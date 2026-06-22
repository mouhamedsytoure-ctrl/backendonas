<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('envois', function (Blueprint $t) {
            $t->id();
            $t->string('beneficiaire');           // a qui on envoie
            $t->string('telephone')->nullable();
            $t->decimal('montant', 12, 2);
            $t->string('motif')->nullable();       // note / raison (facultatif)
            $t->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('envois'); }
};
