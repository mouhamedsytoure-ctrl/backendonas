<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'telephone', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // --- Roles ---
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isLocataire(): bool  { return $this->role === 'locataire'; }

    // --- Relations ---
    public function adminPermissions(): HasMany
    {
        return $this->hasMany(AdminPermission::class);
    }

    // Contrats de l'utilisateur en tant que locataire
    public function contrats(): HasMany
    {
        return $this->hasMany(Contrat::class);
    }

    public function reclamations(): HasMany
    {
        return $this->hasMany(Reclamation::class);
    }

    /**
     * Verifie un droit d'admin. Le super admin a tous les droits.
     * $action : view | create | update | delete
     */
    public function hasPermission(string $module, string $action): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        $perm = $this->adminPermissions()->where('module', $module)->first();
        return $perm ? (bool) $perm->{'can_' . $action} : false;
    }
}
