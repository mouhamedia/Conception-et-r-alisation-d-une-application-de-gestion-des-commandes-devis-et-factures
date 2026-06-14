<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntrepriseRequest;
use App\Models\Entreprise;
use App\Services\EntrepriseContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EntrepriseController extends Controller
{
    public function __construct(private EntrepriseContextService $contexte) {}

    public function showSelect()
    {
        $entreprises = Auth::user()->entreprises()->withPivot('role')->get();

        return view('entreprise.select', compact('entreprises'));
    }

    public function select(Request $request)
    {
        $request->validate(['entreprise_id' => 'required|integer']);

        $entrepriseId = (int) $request->entreprise_id;
        $appartient = Auth::user()->entreprises()->where('entreprise_id', $entrepriseId)->exists();

        if (!$appartient) {
            abort(403, 'Accès non autorisé à cette entreprise.');
        }

        session(['entreprise_id' => $entrepriseId]);

        return redirect()->route('dashboard.index');
    }

    public function create()
    {
        return view('entreprise.create');
    }

    public function store(StoreEntrepriseRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $entreprise = Entreprise::create($data);

        $entreprise->users()->attach(Auth::id(), [
            'role'      => 'owner',
            'joined_at' => now(),
        ]);

        session(['entreprise_id' => $entreprise->id]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'    => true,
                'entreprise' => [
                    'id'       => $entreprise->id,
                    'nom'      => $entreprise->nom,
                    'ville'    => $entreprise->ville,
                    'role'     => 'owner',
                    'logo_url' => $entreprise->logo ? Storage::url($entreprise->logo) : null,
                ],
                'redirect' => route('dashboard.index'),
            ]);
        }

        return redirect()->route('dashboard.index')
            ->with('success', "Entreprise \"{$entreprise->nom}\" créée avec succès !");
    }

    public function edit()
    {
        $entreprise = $this->contexte->getEntreprise();
        $this->authorize('update', $entreprise);

        return view('entreprise.edit', compact('entreprise'));
    }

    public function update(StoreEntrepriseRequest $request)
    {
        $entreprise = $this->contexte->getEntreprise();
        $this->authorize('update', $entreprise);

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($entreprise->logo) {
                Storage::disk('public')->delete($entreprise->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        } else {
            unset($data['logo']);
        }

        $entreprise->update($data);

        return redirect()->route('entreprise.edit')
            ->with('success', 'Informations de l\'entreprise mises à jour.');
    }

    public function equipe()
    {
        $entreprise = $this->contexte->getEntreprise();
        $this->authorize('viewTeam', $entreprise);

        $membres = $entreprise->users()->withPivot('role', 'joined_at')->get();

        return view('entreprise.equipe', compact('entreprise', 'membres'));
    }
}
