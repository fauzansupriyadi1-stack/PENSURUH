<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'balance',
        'is_suspended',
        'suspended_at',
        'phone',
        'slug',
        'password',
        'google_id',
        'avatar',
    ];

    /**
     * Dapatkan slug user untuk URL friendly.
     */
    public function getSlugAttribute()
    {
        if (!empty($this->attributes['slug'])) {
            return $this->attributes['slug'];
        }
        return \Illuminate\Support\Str::slug($this->name ?? 'user') . '-' . $this->id;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_suspended' => 'boolean',
            'suspended_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ordersCreated()
    {
        return $this->hasMany(Order::class, 'penyuruh_id');
    }

    public function ordersTaken()
    {
        return $this->hasMany(Order::class, 'disuruh_id');
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}
