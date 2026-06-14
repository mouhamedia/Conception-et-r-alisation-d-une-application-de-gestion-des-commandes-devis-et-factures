<?php

namespace App\Services;

use App\Models\Entreprise;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationService
{
    public function inviter(Entreprise $entreprise, string $email, string $role): Invitation
    {
        Invitation::where('entreprise_id', $entreprise->id)
            ->where('email', $email)
            ->whereNull('accepted_at')
            ->delete();

        $invitation = Invitation::create([
            'entreprise_id' => $entreprise->id,
            'email' => $email,
            'role' => $role,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::send('emails.invitation', [
            'invitation' => $invitation,
            'lien' => route('invitations.accept', $invitation->token),
        ], function ($message) use ($email, $entreprise) {
            $message->to($email)
                ->subject("Invitation à rejoindre {$entreprise->nom} sur GestiPro");
        });

        return $invitation;
    }

    public function accepter(Invitation $invitation, User $user): void
    {
        $dejamembre = $user->entreprises()
            ->where('entreprise_id', $invitation->entreprise_id)
            ->exists();

        if (!$dejaMembre) {
            $user->entreprises()->attach($invitation->entreprise_id, [
                'role' => $invitation->role,
                'joined_at' => now(),
            ]);
        }

        $invitation->update(['accepted_at' => now()]);

        session(['entreprise_id' => $invitation->entreprise_id]);
    }
}
