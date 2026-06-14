<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    use HasFactory;

    protected $table = 'ligne_commandes';

    protected $fillable = [
        'commande_id',
        'produit_id',
        'quantite',
        'prix_unitaire_snapshot',
        'sous_total',
    ];

    protected $casts = [
        'prix_unitaire_snapshot' => 'decimal:2',
        'sous_total' => 'decimal:2',
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
