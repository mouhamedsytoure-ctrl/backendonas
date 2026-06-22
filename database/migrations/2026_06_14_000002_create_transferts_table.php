<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transferts', function (Blueprint $t) {
            $t->id();
            $t->enum('type', ['envoi', 'retrait']);
            $t->string('service');
            $t->string('client')->nullable();
            $t->string('telephone')->nullable();
            $t->string('destination')->nullable();
            $t->decimal('montant', 12, 2);
            $t->decimal('commission', 12, 2)->default(0);
            $t->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('transferts'); }
};
