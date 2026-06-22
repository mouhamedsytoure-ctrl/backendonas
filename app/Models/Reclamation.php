<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reclamation extends Model
{
    protected $fillable = [
        'user_id', 'logement_id', 'objet', 'description',
        'priorite', 'statut', 'escalade_super_admin', 'traite_par',
    ];

    protected $casts = [
        'escalade_super_admin' => 'boolean',
    ];

    public function locataire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function logement(): BelongsTo
    {
        return $this->belongsTo(Logement::class);
    }

    public function traitePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }
}
