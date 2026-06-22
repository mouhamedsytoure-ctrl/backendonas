<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reclamation;
use Illuminate\Http\Request;

class ReclamationController extends Controller
{
    // GET /api/reclamations
    public function index(Request $request)
    {
        $query = Reclamation::query()->with('locataire', 'logement', 'traitePar');

        // Le locataire ne voit que les siennes
        if ($request->user()->isLocataire()) {
            $query->where('user_id', $request->user()->id);
        }

        return response()->json($query->latest()->get());
    }

    // POST /api/reclamations  (le locataire depose une reclamation)
    public function store(Request $request)
    {
        $data = $request->validate([
            'logement_id' => ['nullable', 'exists:logements,id'],
            'objet'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priorite'    => ['nullable', 'in:basse,normale,haute'],
        ]);
        $data['user_id'] = $request->user()->id;
        $data['statut']  = 'ouvert';

        $reclamation = Reclamation::create($data);
        return response()->json($reclamation, 201);
    }

    // PUT /api/reclamations/{reclamation}  (l'admin traite / escalade)
    public function update(Request $request, Reclamation $reclamation)
    {
        abort_unless($request->user()->hasPermission('reclamations', 'update'), 403);

        $data = $request->validate([
            'statut'               => ['sometimes', 'in:ouvert,en_cours,resolu'],
            'escalade_super_admin' => ['sometimes', 'boolean'],
            'priorite'             => ['sometimes', 'in:basse,normale,haute'],
        ]);
        if ($request->boolean('escalade_super_admin')) {
            $data['escalade_super_admin'] = true;
        }
        $data['traite_par'] = $request->user()->id;

        $reclamation->update($data);
        return response()->json($reclamation->fresh());
    }
}
