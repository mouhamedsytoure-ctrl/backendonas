<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->string('preneur_civilite')->nullable()->after('preneur_prenom'); // Monsieur/Madame/Mademoiselle
            $table->string('composition')->nullable()->after('preneur_piece_numero'); // ex: 01 Sejour, 01 Chambre...
            $table->string('usage')->nullable()->after('composition'); // domestique / commercial
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn(['preneur_civilite', 'composition', 'usage']);
        });
    }
};
