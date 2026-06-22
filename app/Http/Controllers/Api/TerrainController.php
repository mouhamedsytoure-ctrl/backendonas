<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Terrain;
use Illuminate\Http\Request;

class TerrainController extends Controller
{
    public function index()
    {
        return response()->json(Terrain::latest()->get());
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('terrains', 'create'), 403);

        $data = $request->validate([
            'nom'           => ['required', 'string', 'max:255'],
            'ville'         => ['nullable', 'string', 'max:255'],
            'surface'       => ['nullable', 'string', 'max:255'],
            'statut'        => ['nullable', 'in:disponible,constructible,en_projet,vendu'],
            'type_document' => ['nullable', 'in:titre_foncier,bail,autre'],
            'description'   => ['nullable', 'string'],
        ]);
        $data['created_by'] = $request->user()->id;

        return response()->json(Terrain::create($data), 201);
    }

    public function update(Request $request, Terrain $terrain)
    {
        abort_unless($request->user()->hasPermission('terrains', 'update'), 403);

        $data = $request->validate([
            'nom'           => ['sometimes', 'string', 'max:255'],
            'ville'         => ['nullable', 'string', 'max:255'],
            'surface'       => ['nullable', 'string', 'max:255'],
            'statut'        => ['sometimes', 'in:disponible,constructible,en_projet,vendu'],
            'type_document' => ['nullable', 'in:titre_foncier,bail,autre'],
            'description'   => ['nullable', 'string'],
        ]);

        $terrain->update($data);
        return response()->json($terrain);
    }

    public function destroy(Request $request, Terrain $terrain)
    {
        abort_unless($request->user()->hasPermission('terrains', 'delete'), 403);
        $terrain->delete();
        return response()->json(['message' => 'Terrain supprime.']);
    }
}
