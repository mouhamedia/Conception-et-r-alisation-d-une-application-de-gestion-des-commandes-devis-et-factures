<?php

namespace App\Events;

use App\Models\Devis;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DevisValide
{
    use Dispatchable, SerializesModels;

    public function __construct(public Devis $devis) {}
}
