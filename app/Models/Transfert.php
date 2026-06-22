<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfert extends Model
{
    protected $fillable = ['type', 'service', 'client', 'telephone', 'destination', 'montant', 'commission', 'agent_id'];
    protected $casts = ['montant' => 'float', 'commission' => 'float'];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
