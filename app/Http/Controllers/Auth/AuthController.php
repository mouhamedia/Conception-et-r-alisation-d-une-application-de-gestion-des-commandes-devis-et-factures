<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Invitation;
use App\Models\User;
use App\Services\InvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Identifiants incorrects.'])->withInput();
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->entreprises()->count() === 1) {
            $entreprise = $user->entreprises()->first();
            session(['entreprise_id' => $entreprise->id]);
            return redirect()->route('dashboard.index');
        }

        return redirect()->route('entreprise.select');
    }

    public function showRegister()
    {
        return view('auth.register', [
            'invitation' => $this->invitationEnAttente(),
        ]);
    }

    public function register(RegisterRequest $request, InvitationService $invitationService)
    {
        $user = User::create([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $invitation = $this->invitationEnAttente();
        session()->forget('invitation_token');

        if ($invitation) {
            $invitationService->accepter($invitation, $user);

            return redirect()->route('dashboard.index')
                ->with('success', "Compte créé ! Vous avez rejoint {$invitation->entreprise->nom}.");
        }

        return redirect()->route('entreprise.create')
            ->with('success', 'Compte créé avec succès ! Créez votre première entreprise.');
    }

    private function invitationEnAttente(): ?Invitation
    {
        $token = session('invitation_token');

        if (!$token) {
            return null;
        }

        return Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
