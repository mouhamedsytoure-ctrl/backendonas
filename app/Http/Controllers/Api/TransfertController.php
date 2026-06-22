<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bareme;
use App\Models\Transfert;
use Illuminate\Http\Request;

class TransfertController extends Controller
{
    // GET /api/transferts?mois=YYYY-MM&service=&type=&q=
    public function index(Request $request)
    {
        abort_if($request->user()->isLocataire(), 403);

        $query = Transfert::query()->with('agent:id,name');

        if ($request->filled('mois')) {
            // mois au format YYYY-MM
            [$y, $m] = array_pad(explode('-', $request->mois), 2, null);
            if ($y && $m) $query->whereYear('created_at', $y)->whereMonth('created_at', $m);
        }
        if ($request->filled('service')) $query->where('service', $request->service);
        if ($request->filled('type'))    $query->where('type', $request->type);
        if ($request->filled('q'))       $query->where('client', 'like', '%' . $request->q . '%');

        return response()->json($query->latest()->get());
    }

    // POST /api/transferts  { type, service, montant, client?, telephone?, destination? }
    public function store(Request $request)
    {
        // Seul l'agent (admin) saisit. Le super admin observe uniquement.
        abort_unless($request->user()->isAdmin(), 403, "Le super admin ne peut pas saisir d'operations.");

        $data = $request->validate([
            'type'        => ['required', 'in:envoi,retrait'],
            'service'     => ['required', 'string'],
            'montant'     => ['required', 'numeric', 'min:0'],
            'client'      => ['nullable', 'string'],
            'telephone'   => ['nullable', 'string'],
            'destination' => ['nullable', 'string'],
        ]);

        // commission calculee cote serveur (jamais depuis le client)
        $bareme = Bareme::where('service', $data['service'])->where('type', $data['type'])->first();
        $data['commission'] = $bareme ? $bareme->commissionPour((float) $data['montant']) : 0;
        $data['agent_id'] = $request->user()->id;

        $t = Transfert::create($data);
        return response()->json($t->load('agent:id,name'), 201);
    }

    // DELETE /api/transferts/{transfert}
    public function destroy(Request $request, Transfert $transfert)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $transfert->delete();
        return response()->json(['deleted' => true]);
    }
}