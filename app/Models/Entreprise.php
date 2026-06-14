<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'siret',
        'email',
        'telephone',
        'adresse',
        'ville',
        'pays',
        'logo',
        'devise',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'entreprise_user')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function produits()
    {
        return $this->hasMany(Produit::class);
    }

    public function categories()
    {
        return $this->hasMany(\App\Models\Categorie::class);
    }

    public function devis()
    {
        return $this->hasMany(Devis::class);
    }

    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function owners()
    {
        return $this->belongsToMany(User::class, 'entreprise_user')
            ->wherePivot('role', 'owner')
            ->withPivot('role', 'joined_at');
    }

    public function demandesRecues()
    {
        return $this->hasMany(DemandeDevis::class, 'entreprise_cible_id');
    }

    public function demandesEnvoyees()
    {
        return $this->hasMany(DemandeDevis::class, 'entreprise_source_id');
    }
}
