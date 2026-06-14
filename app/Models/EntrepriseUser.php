<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EntrepriseUser extends Pivot
{
    protected $table = 'entreprise_user';

    protected $fillable = [
        'user_id',
        'entreprise_id',
        'role',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];
}
