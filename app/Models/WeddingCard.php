<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class WeddingCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'design_template_id',
        'title',
        'card_details',
        'custom_message',
        'is_published',
        'approval_status',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'unique_url',
        'expiry_date',
    ];

    protected $casts = [
        'card_details' => 'array',
        'is_published' => 'boolean',
        'approved_at' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($weddingCard) {
            if (empty($weddingCard->unique_url)) {
                $weddingCard->unique_url = Str::random(10);
            }
        });
    }

    /**
     * Get the user that owns the wedding card.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the design template used by the wedding card.
     */
    public function designTemplate()
    {
        return $this->belongsTo(DesignTemplate::class);
    }

    /**
     * Get the analytics for the wedding card.
     */
    public function analytics()
    {
        return $this->hasMany(CardAnalytic::class);
    }

    /**
     * Get the RSVPs for the wedding card.
     */
    public function rsvps()
    {
        return $this->hasMany(Rsvp::class);
    }

    /**
     * Get the gifts for the wedding card.
     */
    public function gifts()
    {
        return $this->hasMany(Gift::class);
    }

    /**
     * Scope to get only published cards.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get only active (non-expired) cards.
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>', now());
        });
    }

    /**
     * Scope to get only expired cards.
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<=', now());
    }

    /**
     * Check if the card is expired.
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Get the full URL for viewing the card.
     */
    public function getViewUrlAttribute()
    {
        return route('wedding-card.view', $this->unique_url);
    }

    /**
     * Check if a user has reached their card limit.
     */
    public static function userHasReachedLimit($userId)
    {
        $maxCards = \App\Models\Setting::get('max_cards_per_user', 10);
        $userCardCount = static::where('user_id', $userId)->count();
        return $userCardCount >= $maxCards;
    }

    /**
     * Get remaining cards for a user.
     */
    public static function getRemainingCardsForUser($userId)
    {
        $maxCards = \App\Models\Setting::get('max_cards_per_user', 10);
        $userCardCount = static::where('user_id', $userId)->count();
        return max(0, $maxCards - $userCardCount);
    }

    /**
     * Get the admin who approved this card.
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to get only pending approval cards.
     */
    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', 'pending');
    }

    /**
     * Scope to get only approved cards.
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    /**
     * Scope to get only rejected cards.
     */
    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    /**
     * Check if the card is pending approval.
     */
    public function isPending()
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if the card is approved.
     */
    public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if the card is rejected.
     */
    public function isRejected()
    {
        return $this->approval_status === 'rejected';
    }

    /**
     * Approve the card.
     */
    public function approve($adminId)
    {
        $this->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => null,
            'is_published' => true, // Auto-publish when approved
        ]);
    }

    /**
     * Reject the card.
     */
    public function reject($adminId, $reason = null)
    {
        $this->update([
            'approval_status' => 'rejected',
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => $reason,
            'is_published' => false,
        ]);
    }
}
