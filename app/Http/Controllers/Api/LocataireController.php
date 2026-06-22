<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use App\Models\Logement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LocataireController extends Controller
{
    // GET /api/locataires
    public function index()
    {
        $locataires = User::where('role', 'locataire')
            ->with(['contrats' => fn ($q) => $q->where('statut', 'actif')->with('logement.immeuble')])
            ->get();

        return response()->json($locataires);
    }

    /**
     * POST /api/locataires
     * L'admin cree le COMPTE locataire ET son contrat, et passe le logement en "loue".
     */
    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('locataires', 'create'), 403);

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'telephone'     => ['nullable', 'string', 'max:255'],
            'password'      => ['nullable', 'string', 'min:6'],
            'logement_id'   => ['required', 'exists:logements,id'],
            'date_debut'    => ['required', 'date'],
            'date_fin'      => ['nullable', 'date', 'after:date_debut'],
            'montant_loyer' => ['required', 'numeric', 'min:0'],
            'caution'       => ['nullable', 'numeric', 'min:0'],
            'jour_echeance' => ['nullable', 'integer', 'between:1,31'],
        ]);

        // Mot de passe : fourni par l'admin, ou genere automatiquement
        $genere = ! $request->filled('password');
        $plain  = $genere ? Str::random(8) : $data['password'];

        $res = DB::transaction(function () use ($data, $plain) {
            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'telephone' => $data['telephone'] ?? null,
                'password'  => Hash::make($plain),
                'role'      => 'locataire',
                'is_active' => true,
            ]);

            $contrat = Contrat::create([
                'logement_id'   => $data['logement_id'],
                'user_id'       => $user->id,
                'date_debut'    => $data['date_debut'],
                'date_fin'      => $data['date_fin'] ?? null,
                'montant_loyer' => $data['montant_loyer'],
                'caution'       => $data['caution'] ?? 0,
                'jour_echeance' => $data['jour_echeance'] ?? 5,
                'statut'        => 'actif',
            ]);

            Logement::where('id', $data['logement_id'])->update(['statut' => 'loue']);

            return [$user, $contrat];
        });

        return response()->json([
            'locataire'    => $res[0],
            'contrat'      => $res[1],
            'mot_de_passe' => $genere ? $plain : null, // a communiquer au locataire si genere
        ], 201);
    }
}
