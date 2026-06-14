<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $entrepriseId = session('entreprise_id');
        $user = Auth::user();

        $role = $user->getRoleInEntreprise((int) $entrepriseId);

        if (!in_array($role, $roles)) {
            abort(403, 'Vous n\'avez pas les droits nécessaires pour cette action.');
        }

        return $next($request);
    }
}
