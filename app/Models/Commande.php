<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'entreprise_id',
        'devis_id',
        'numero',
        'client_nom',
        'statut',
        'sous_total_ht',
        'tva',
        'total_ttc',
        'notes',
    ];

    protected $casts = [
        'sous_total_ht' => 'decimal:2',
        'tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }

    public function lignes()
    {
        return $this->hasMany(LigneCommande::class);
    }

    public function facture()
    {
        return $this->hasOne(Facture::class);
    }
}
