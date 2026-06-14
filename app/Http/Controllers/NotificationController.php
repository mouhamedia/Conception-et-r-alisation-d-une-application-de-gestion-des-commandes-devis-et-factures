<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\EntrepriseContextService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(private EntrepriseContextService $contexte) {}

    public function index()
    {
        $entreprise = $this->contexte->getEntreprise();
        $notifications = Auth::user()->notifications()
            ->where('entreprise_id', $entreprise->id)
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function marquerLu(Notification $notification)
    {
        $this->verifierAppartenance($notification);
        $notification->update(['lu' => true]);

        return back()->with('success', 'Notification marquée comme lue.');
    }

    public function marquerTousLus()
    {
        $entreprise = $this->contexte->getEntreprise();
        Auth::user()->notifications()
            ->where('entreprise_id', $entreprise->id)
            ->where('lu', false)
            ->update(['lu' => true]);

        return back()->with('success', 'Toutes les notifications marquées comme lues.');
    }

    private function verifierAppartenance(Notification $notification): void
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
