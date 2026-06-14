<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $fillable = ['entreprise_id', 'nom'];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function produits()
    {
        return $this->hasMany(Produit::class, 'categorie', 'nom')
                    ->where('entreprise_id', $this->entreprise_id);
    }
}
