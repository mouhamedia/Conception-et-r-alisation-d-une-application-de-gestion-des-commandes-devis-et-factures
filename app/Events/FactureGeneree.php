<?php

namespace App\Events;

use App\Models\Facture;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FactureGeneree
{
    use Dispatchable, SerializesModels;

    public function __construct(public Facture $facture) {}
}
