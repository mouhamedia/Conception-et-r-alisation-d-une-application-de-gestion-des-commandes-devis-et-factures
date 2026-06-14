<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckEntrepriseAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $entrepriseId = session('entreprise_id');

        if (!$entrepriseId) {
            return redirect()->route('entreprise.select')
                ->with('info', 'Veuillez sélectionner une entreprise.');
        }

        $appartient = Auth::user()
            ->entreprises()
            ->where('entreprise_id', $entrepriseId)
            ->exists();

        if (!$appartient) {
            session()->forget('entreprise_id');
            return redirect()->route('entreprise.select')
                ->withErrors(['error' => 'Accès non autorisé à cette entreprise.']);
        }

        return $next($request);
    }
}
