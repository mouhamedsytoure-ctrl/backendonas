<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->string('preneur_nom')->nullable()->after('user_id');
            $table->string('preneur_prenom')->nullable()->after('preneur_nom');
            $table->string('preneur_telephone')->nullable()->after('preneur_prenom');
            $table->string('preneur_email')->nullable()->after('preneur_telephone');
            $table->string('preneur_adresse')->nullable()->after('preneur_email');
            $table->string('preneur_profession')->nullable()->after('preneur_adresse');
            $table->string('preneur_nationalite')->nullable()->after('preneur_profession');
            $table->date('preneur_date_naissance')->nullable()->after('preneur_nationalite');
            $table->string('preneur_lieu_naissance')->nullable()->after('preneur_date_naissance');
            $table->enum('preneur_piece_type', ['cni', 'passeport', 'permis', 'autre'])->nullable()->after('preneur_lieu_naissance');
            $table->string('preneur_piece_numero')->nullable()->after('preneur_piece_type');

            $table->boolean('est_bloque')->default(false)->after('statut');
            $table->string('motif_fin')->nullable()->after('est_bloque');
            $table->timestamp('archived_at')->nullable()->after('motif_fin');
        });

        Schema::table('medias', function (Blueprint $table) {
            // pour distinguer : piece_identite, signature, ou null (photo normale)
            $table->string('libelle')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn([
                'preneur_nom', 'preneur_prenom', 'preneur_telephone', 'preneur_email',
                'preneur_adresse', 'preneur_profession', 'preneur_nationalite',
                'preneur_date_naissance', 'preneur_lieu_naissance',
                'preneur_piece_type', 'preneur_piece_numero',
                'est_bloque', 'motif_fin', 'archived_at',
            ]);
        });
        Schema::table('medias', function (Blueprint $table) {
            $table->dropColumn('libelle');
        });
    }
};
