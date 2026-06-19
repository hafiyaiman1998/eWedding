<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
    ];

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
            'password' => 'hashed',
        ];
    }

    /**
     * Get the wedding cards created by this user.
     */
    public function weddingCards()
    {
        return $this->hasMany(WeddingCard::class);
    }

    /**
     * Get the user's preferences.
     */
    public function preferences()
    {
        return $this->hasOne(\App\Models\UserPreference::class);
    }

    /**
     * Get the user's preferences or create default ones.
     */
    public function getPreferences()
    {
        if (!$this->preferences) {
            return \App\Models\UserPreference::createDefaults($this->id);
        }
        return $this->preferences;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->type === 'admin';
    }

    /**
     * Check if user is regular user/client.
     */
    public function isUser()
    {
        return $this->type === 'user';
    }

    /**
     * Scope to get only admin users.
     */
    public function scopeAdmins($query)
    {
        return $query->where('type', 'admin');
    }

    /**
     * Scope to get only regular users/clients.
     */
    public function scopeClients($query)
    {
        return $query->where('type', 'user');
    }
}
