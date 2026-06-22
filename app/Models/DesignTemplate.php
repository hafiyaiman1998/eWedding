<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DesignTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'blade_template',
        'full_html_template',
        'category',
        'is_malaysian_design',
        'preview_image',
        'default_variables',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_variables' => 'array',
            'is_malaysian_design' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the wedding cards that use this template.
     */
    public function weddingCards(): HasMany
    {
        return $this->hasMany(WeddingCard::class);
    }

    /**
     * Scope to get only Malaysian designs.
     */
    public function scopeMalaysian(Builder $query): Builder
    {
        return $query->where('is_malaysian_design', true);
    }

    /**
     * Scope to get only active templates.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
