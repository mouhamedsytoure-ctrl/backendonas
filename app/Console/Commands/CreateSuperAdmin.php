<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    protected $signature = 'toursen:super-admin';
    protected $description = 'Creer un compte super admin';

    public function handle(): int
    {
        $name = $this->ask('Nom complet');
        $email = $this->ask('Email');

        if (User::where('email', $email)->exists()) {
            $this->error('Cet email est deja utilise.');
            return self::FAILURE;
        }

        $password = $this->secret('Mot de passe');
        $telephone = $this->ask('Telephone (laisser vide si aucun)') ?: null;

        User::create([
            'name'      => $name,
            'email'     => $email,
            'telephone' => $telephone,
            'password'  => Hash::make($password),
            'role'      => 'super_admin',
            'is_active' => true,
        ]);

        $this->info("Super admin cree : $email");
        return self::SUCCESS;
    }
}