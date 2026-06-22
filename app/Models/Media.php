<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'medias';

    protected $fillable = [
        'mediable_id', 'mediable_type', 'type', 'chemin', 'couverture', 'ordre',
    ];

    protected $casts = [
        'couverture' => 'boolean',
        'ordre'      => 'integer',
    ];

    // L'URL complete est ajoutee automatiquement aux reponses JSON
    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->chemin);
    }

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
