<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function envoyer(User $user, int $entrepriseId, string $type, string $titre, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'entreprise_id' => $entrepriseId,
            'type' => $type,
            'titre' => $titre,
            'message' => $message,
            'lu' => false,
            'data' => $data,
        ]);
    }

    public function envoyerATous(int $entrepriseId, string $type, string $titre, string $message, array $data = []): void
    {
        $users = \App\Models\Entreprise::findOrFail($entrepriseId)->users;

        foreach ($users as $user) {
            $this->envoyer($user, $entrepriseId, $type, $titre, $message, $data);
        }
    }
}
