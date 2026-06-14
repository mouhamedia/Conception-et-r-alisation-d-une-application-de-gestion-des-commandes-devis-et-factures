<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'entreprise_id',
        'commande_id',
        'numero',
        'statut',
        'montant_paye',
        'date_echeance',
        'payee_at',
        'notes',
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'payee_at' => 'datetime',
        'montant_paye' => 'decimal:2',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function getMontantTotalAttribute(): float
    {
        return (float) $this->commande->total_ttc;
    }

    public function getMontantRestantAttribute(): float
    {
        return $this->montant_total - (float) $this->montant_paye;
    }
}
