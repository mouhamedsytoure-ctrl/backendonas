<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Envoi;
use Illuminate\Http\Request;

class EnvoiController extends Controller
{
    // GET /api/envois?mois=YYYY-MM&q=
    public function index(Request $request)
    {
        abort_if($request->user()->isLocataire(), 403); // agent + super admin

        $query = Envoi::query()->with('agent:id,name');
        if ($request->filled('mois')) {
            [$y, $m] = array_pad(explode('-', $request->mois), 2, null);
            if ($y && $m) $query->whereYear('created_at', $y)->whereMonth('created_at', $m);
        }
        if ($request->filled('q')) $query->where('beneficiaire', 'like', '%' . $request->q . '%');

        return response()->json($query->latest()->get());
    }

    // POST /api/envois  (seul l'agent saisit)
    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403, "Le super admin ne peut pas saisir d'envois.");

        $data = $request->validate([
            'beneficiaire' => ['required', 'string', 'max:255'],
            'telephone'    => ['nullable', 'string', 'max:255'],
            'montant'      => ['required', 'numeric', 'min:0'],
            'motif'        => ['nullable', 'string', 'max:255'],
        ]);
        $data['agent_id'] = $request->user()->id;

        $e = Envoi::create($data);
        return response()->json($e->load('agent:id,name'), 201);
    }

    // DELETE /api/envois/{envoi}  (seul l'agent)
    public function destroy(Request $request, Envoi $envoi)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $envoi->delete();
        return response()->json(['deleted' => true]);
    }
}
