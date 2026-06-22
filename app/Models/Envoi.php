<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Envoi extends Model
{
    protected $fillable = ['beneficiaire', 'telephone', 'montant', 'motif', 'agent_id'];
    protected $casts = ['montant' => 'float'];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
