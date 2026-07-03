<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nid_number',
        'phone',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            $user->officer()->update(['is_oc' => false]);
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function officer(): HasOne
    {
        return $this->hasOne(Officer::class);
    }
}
