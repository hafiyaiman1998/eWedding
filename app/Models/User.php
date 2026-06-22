<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
    public function weddingCards(): HasMany
    {
        return $this->hasMany(WeddingCard::class);
    }

    /**
     * Get the user's preferences.
     */
    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Get the user's preferences or create default ones.
     */
    public function getPreferences(): UserPreference
    {
        if (! $this->preferences) {
            return UserPreference::createDefaults($this->id);
        }

        return $this->preferences;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->type === UserType::Admin->value;
    }

    /**
     * Check if user is regular user/client.
     */
    public function isUser(): bool
    {
        return $this->type === UserType::User->value;
    }

    /**
     * Scope to get only admin users.
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('type', UserType::Admin->value);
    }

    /**
     * Scope to get only regular users/clients.
     */
    public function scopeClients(Builder $query): Builder
    {
        return $query->where('type', UserType::User->value);
    }
}
