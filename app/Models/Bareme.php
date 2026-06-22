<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bareme extends Model
{
    protected $fillable = ['service', 'type', 'mode', 'valeur', 'paliers'];
    protected $casts = ['paliers' => 'array', 'valeur' => 'float'];

    /** Calcule la commission pour un montant donne selon ce bareme. */
    public function commissionPour(float $montant): float
    {
        if ($montant <= 0) return 0;
        if ($this->mode === 'percent') return round($montant * (float) $this->valeur / 100);
        if ($this->mode === 'fixe') return (float) $this->valeur;
        // paliers : [{max, comm}, ...]
        $ps = collect($this->paliers ?? [])->sortBy('max')->values();
        foreach ($ps as $p) {
            if ($montant <= (float) ($p['max'] ?? 0)) return (float) ($p['comm'] ?? 0);
        }
        return $ps->count() ? (float) ($ps->last()['comm'] ?? 0) : 0;
    }
}
