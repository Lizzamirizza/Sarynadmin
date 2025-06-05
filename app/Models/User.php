<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens; // ✅ Tambahkan untuk Sanctum

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // ✅ Tambahkan HasApiTokens

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
    'name',
    'username',
    'email',
    'password',
    'address',
    'province',
    'city',
    'subcity',
    'postalcode',
    'phone',
];


    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi: 1 user punya banyak orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
