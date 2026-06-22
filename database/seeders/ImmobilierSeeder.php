<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use App\Models\Contrat;
use App\Models\Immeuble;
use App\Models\Logement;
use App\Models\Paiement;
use App\Models\Reclamation;
use App\Models\Terrain;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ImmobilierSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Super admin (le pere) ----------
        $superAdmin = User::create([
            'name'      => 'El Hadji Fall',
            'email'     => 'superadmin@toursen.sn',
            'password'  => Hash::make('password'),
            'role'      => 'super_admin',
            'telephone' => '77 123 45 67',
            'is_active' => true,
        ]);

        // ---------- Admin (l'agent) ----------
        $admin = User::create([
            'name'      => 'Cheikh Diouf',
            'email'     => 'admin@toursen.sn',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'telephone' => '76 987 65 43',
            'is_active' => true,
        ]);

        // Droits de l'admin : Voir + Ajouter + Modifier partout, mais PAS Supprimer
        $modules = ['immeubles', 'terrains', 'logements', 'locataires', 'contrats', 'loyers', 'reclamations'];
        foreach ($modules as $module) {
            AdminPermission::create([
                'user_id'    => $admin->id,
                'module'     => $module,
                'can_view'   => true,
                'can_create' => true,
                'can_update' => true,
                'can_delete' => false,
            ]);
        }

        // ---------- Immeuble : Residence Teranga ----------
        $teranga = Immeuble::create([
            'nom'        => 'Residence Teranga',
            'adresse'    => 'Avenue Lamine Gueye',
            'ville'      => 'Dakar - Plateau',
            'created_by' => $superAdmin->id,
        ]);

        // Logements : 3e etage (A appart, B studio, C mini studio dispo) + 2e etage
        $apptTeranga3A = Logement::create([
            'immeuble_id' => $teranga->id, 'reference' => '3A', 'etage' => 3,
            'type' => 'appartement', 'loyer' => 185000, 'statut' => 'loue', 'nb_pieces' => 3,
        ]);
        $studioTeranga3B = Logement::create([
            'immeuble_id' => $teranga->id, 'reference' => '3B', 'etage' => 3,
            'type' => 'studio', 'loyer' => 180000, 'statut' => 'loue', 'nb_pieces' => 1,
        ]);
        Logement::create([
            'immeuble_id' => $teranga->id, 'reference' => '3C', 'etage' => 3,
            'type' => 'mini_studio', 'loyer' => 120000, 'statut' => 'disponible', 'nb_pieces' => 1,
        ]);
        Logement::create([
            'immeuble_id' => $teranga->id, 'reference' => '2A', 'etage' => 2,
            'type' => 'appartement', 'loyer' => 185000, 'statut' => 'loue', 'nb_pieces' => 3,
        ]);

        // ---------- Immeuble : Residence Saly ----------
        $saly = Immeuble::create([
            'nom'        => 'Residence Saly',
            'adresse'    => 'Route de la Plage',
            'ville'      => 'Mbour - Saly',
            'created_by' => $superAdmin->id,
        ]);
        $logSaly = Logement::create([
            'immeuble_id' => $saly->id, 'reference' => '1A', 'etage' => 1,
            'type' => 'appartement', 'loyer' => 150000, 'statut' => 'loue', 'nb_pieces' => 2,
        ]);
        Logement::create([
            'immeuble_id' => $saly->id, 'reference' => '1C', 'etage' => 1,
            'type' => 'mini_studio', 'loyer' => 90000, 'statut' => 'disponible', 'nb_pieces' => 1,
        ]);

        // ---------- Terrain ----------
        Terrain::create([
            'nom' => 'Terrain Diamniadio', 'ville' => 'Diamniadio', 'surface' => '500 m2',
            'statut' => 'constructible', 'type_document' => 'titre_foncier', 'created_by' => $superAdmin->id,
        ]);

        // ---------- Locataires (comptes) ----------
        $awa = User::create([
            'name' => 'Awa Diop', 'email' => 'awa@locataire.sn',
            'password' => Hash::make('password'), 'role' => 'locataire', 'telephone' => '78 222 11 00',
        ]);
        $ibrahima = User::create([
            'name' => 'Ibrahima Fall', 'email' => 'ibrahima@locataire.sn',
            'password' => Hash::make('password'), 'role' => 'locataire', 'telephone' => '70 555 44 33',
        ]);

        // ---------- Contrats ----------
        $contratAwa = Contrat::create([
            'logement_id' => $studioTeranga3B->id, 'user_id' => $awa->id,
            'date_debut' => '2024-03-01', 'date_fin' => '2026-02-28',
            'montant_loyer' => 180000, 'caution' => 360000, 'jour_echeance' => 1, 'statut' => 'actif',
        ]);
        $contratIbrahima = Contrat::create([
            'logement_id' => $logSaly->id, 'user_id' => $ibrahima->id,
            'date_debut' => '2024-09-01', 'date_fin' => '2025-08-31',
            'montant_loyer' => 150000, 'caution' => 300000, 'jour_echeance' => 5, 'statut' => 'actif',
        ]);

        // ---------- Paiements ----------
        // Paye : le recu_numero est genere automatiquement par le modele
        Paiement::create([
            'contrat_id' => $contratAwa->id, 'periode' => '2026-04', 'montant' => 180000,
            'mode_paiement' => 'wave', 'statut' => 'paye', 'enregistre_par' => $admin->id,
        ]);
        Paiement::create([
            'contrat_id' => $contratAwa->id, 'periode' => '2026-05', 'montant' => 180000,
            'mode_paiement' => 'orange_money', 'statut' => 'paye', 'enregistre_par' => $admin->id,
        ]);
        // Impaye : pas de recu
        Paiement::create([
            'contrat_id' => $contratIbrahima->id, 'periode' => '2026-05', 'montant' => 150000,
            'statut' => 'impaye',
        ]);

        // ---------- Reclamations ----------
        Reclamation::create([
            'user_id' => $awa->id, 'logement_id' => $studioTeranga3B->id,
            'objet' => "Probleme d'electricite cuisine", 'statut' => 'en_cours',
            'priorite' => 'normale', 'traite_par' => $admin->id,
        ]);
        Reclamation::create([
            'user_id' => $ibrahima->id, 'logement_id' => $logSaly->id,
            'objet' => "Serrure porte d'entree", 'statut' => 'resolu', 'priorite' => 'basse',
            'traite_par' => $admin->id,
        ]);
    }
}
