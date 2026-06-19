<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    protected $casts = [
        'default_variables' => 'array',
        'is_malaysian_design' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the wedding cards that use this template.
     */
    public function weddingCards()
    {
        return $this->hasMany(WeddingCard::class);
    }

    /**
     * Scope to get only Malaysian designs.
     */
    public function scopeMalaysian($query)
    {
        return $query->where('is_malaysian_design', true);
    }

    /**
     * Scope to get only active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
