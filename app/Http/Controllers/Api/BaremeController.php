<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bareme;
use Illuminate\Http\Request;

class BaremeController extends Controller
{
    private array $services = ['western_union', 'ria', 'orange_money'];

    // Valeurs par defaut (creees au premier appel si la table est vide)
    private function defauts(): array
    {
        return [
            ['service' => 'western_union', 'type' => 'envoi',   'mode' => 'percent', 'valeur' => 2, 'paliers' => null],
            ['service' => 'western_union', 'type' => 'retrait', 'mode' => 'fixe',    'valeur' => 1000, 'paliers' => null],
            ['service' => 'ria',           'type' => 'envoi',   'mode' => 'paliers', 'valeur' => null, 'paliers' => [['max' => 25000, 'comm' => 300], ['max' => 100000, 'comm' => 1000], ['max' => 500000, 'comm' => 2500]]],
            ['service' => 'ria',           'type' => 'retrait', 'mode' => 'fixe',    'valeur' => 800, 'paliers' => null],
            ['service' => 'orange_money',  'type' => 'envoi',   'mode' => 'percent', 'valeur' => 1, 'paliers' => null],
            ['service' => 'orange_money',  'type' => 'retrait', 'mode' => 'paliers', 'valeur' => null, 'paliers' => [['max' => 25000, 'comm' => 200], ['max' => 100000, 'comm' => 600], ['max' => 500000, 'comm' => 1500]]],
        ];
    }

    public function index(Request $request)
    {
        abort_if($request->user()->isLocataire(), 403);
        if (Bareme::count() === 0) {
            foreach ($this->defauts() as $d) Bareme::create($d);
        }
        return response()->json(Bareme::orderBy('service')->orderBy('type')->get());
    }

    // PUT /api/baremes  { service, type, mode, valeur?, paliers? }
    public function update(Request $request)
    {
        // Seul l'agent (admin) modifie les baremes.
        abort_unless($request->user()->isAdmin(), 403, 'Modification reservee a l\'agent.');
        $data = $request->validate([
            'service' => ['required', 'string'],
            'type'    => ['required', 'in:envoi,retrait'],
            'mode'    => ['required', 'in:percent,fixe,paliers'],
            'valeur'  => ['nullable', 'numeric'],
            'paliers' => ['nullable', 'array'],
        ]);

        $b = Bareme::updateOrCreate(
            ['service' => $data['service'], 'type' => $data['type']],
            ['mode' => $data['mode'], 'valeur' => $data['valeur'] ?? null, 'paliers' => $data['paliers'] ?? null]
        );
        return response()->json($b);
    }
}