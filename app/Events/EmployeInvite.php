<?php

namespace App\Events;

use App\Models\Invitation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeInvite
{
    use Dispatchable, SerializesModels;

    public function __construct(public Invitation $invitation) {}
}
