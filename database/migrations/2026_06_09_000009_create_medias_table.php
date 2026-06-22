<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            // mediable_id + mediable_type -> attachable a un immeuble OU un logement
            $table->morphs('mediable');
            $table->enum('type', ['photo', 'video'])->default('photo');
            $table->string('chemin');                  // chemin/URL du fichier
            $table->boolean('couverture')->default(false);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medias');
    }
};
