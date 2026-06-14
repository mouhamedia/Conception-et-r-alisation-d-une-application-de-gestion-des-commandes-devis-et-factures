<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'entreprise_id',
        'nom',
        'description',
        'reference_sku',
        'prix_unitaire',
        'stock_actuel',
        'stock_minimum',
        'categorie',
        'actif',
    ];

    protected $casts = [
        'prix_unitaire' => 'decimal:2',
        'actif' => 'boolean',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function lignesDevis()
    {
        return $this->hasMany(LigneDevis::class);
    }

    public function lignesCommandes()
    {
        return $this->hasMany(LigneCommande::class);
    }

    public function isStockFaible(): bool
    {
        return $this->stock_actuel <= $this->stock_minimum;
    }
}
