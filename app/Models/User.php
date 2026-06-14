<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'password',
        'telephone',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function entreprises()
    {
        return $this->belongsToMany(Entreprise::class, 'entreprise_user')
            ->withPivot('role', 'joined_at');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function getRoleInEntreprise(int $entrepriseId): ?string
    {
        $pivot = $this->entreprises()->wherePivot('entreprise_id', $entrepriseId)->first();
        return $pivot?->pivot->role;
    }

    public function isOwnerOf(int $entrepriseId): bool
    {
        return $this->getRoleInEntreprise($entrepriseId) === 'owner';
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->name . ' ' . $this->prenom);
    }
}
