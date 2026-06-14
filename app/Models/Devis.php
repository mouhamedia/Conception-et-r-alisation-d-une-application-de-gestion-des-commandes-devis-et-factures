<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    use HasFactory;

    protected $fillable = [
        'entreprise_id',
        'user_id',
        'numero',
        'client_nom',
        'client_email',
        'client_telephone',
        'client_adresse',
        'statut',
        'sous_total_ht',
        'tva',
        'total_ttc',
        'date_emission',
        'date_expiration',
        'notes',
    ];

    protected $casts = [
        'date_emission' => 'date',
        'date_expiration' => 'date',
        'sous_total_ht' => 'decimal:2',
        'tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lignes()
    {
        return $this->hasMany(LigneDevis::class);
    }

    public function commande()
    {
        return $this->hasOne(Commande::class);
    }

    public function isExpire(): bool
    {
        return $this->date_expiration->isPast() && $this->statut === 'envoye';
    }
}
