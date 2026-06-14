<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteEmployeeRequest;
use App\Models\Invitation;
use App\Services\EntrepriseContextService;
use App\Services\InvitationService;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function __construct(
        private EntrepriseContextService $contexte,
        private InvitationService $invitationService
    ) {}

    public function invite(InviteEmployeeRequest $request)
    {
        $entreprise = $this->contexte->getEntreprise();
        $this->authorize('viewTeam', $entreprise);

        $this->invitationService->inviter(
            $entreprise,
            $request->email,
            $request->role
        );

        return back()->with('success', "Invitation envoyée à {$request->email}.");
    }

    public function accept(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->isExpired()) {
            return redirect()->route('login')
                ->withErrors(['token' => 'Cette invitation a expiré.']);
        }

        if ($invitation->isAccepted()) {
            return redirect()->route('login')
                ->with('info', 'Cette invitation a déjà été acceptée.');
        }

        if (!Auth::check()) {
            session(['invitation_token' => $token]);
            return redirect()->route('register')
                ->with('info', "Créez un compte pour rejoindre {$invitation->entreprise->nom}.");
        }

        $this->invitationService->accepter($invitation, Auth::user());

        return redirect()->route('dashboard.index')
            ->with('success', "Vous avez rejoint {$invitation->entreprise->nom} !");
    }
}
