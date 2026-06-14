<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeDevis extends Model
{
    use HasFactory;

    protected $table = 'demandes_devis';

    protected $fillable = [
        'entreprise_source_id',
        'entreprise_cible_id',
        'user_id',
        'description',
        'budget',
        'statut',
        'devis_id',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
    ];

    public function entrepriseSource()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_source_id');
    }

    public function entrepriseCible()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_cible_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function devis()
    {
        return $this->belongsTo(Devis::class);
    }

    public function messages()
    {
        return $this->hasMany(DemandeMessage::class, 'demande_id')->orderBy('created_at');
    }
}
