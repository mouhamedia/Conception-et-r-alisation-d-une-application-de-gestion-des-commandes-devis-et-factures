<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeMessage extends Model
{
    protected $table = 'demande_messages';

    protected $fillable = ['demande_id', 'entreprise_id', 'user_id', 'contenu'];

    public function demande()
    {
        return $this->belongsTo(DemandeDevis::class, 'demande_id');
    }

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
