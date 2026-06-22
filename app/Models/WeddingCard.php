<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'card_details' => 'array',
            'is_published' => 'boolean',
            'approved_at' => 'datetime',
            'expiry_date' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (WeddingCard $weddingCard): void {
            if (empty($weddingCard->unique_url)) {
                $weddingCard->unique_url = Str::random(10);
            }
        });
    }

    /**
     * Get the user that owns the wedding card.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the design template used by the wedding card.
     */
    public function designTemplate(): BelongsTo
    {
        return $this->belongsTo(DesignTemplate::class);
    }

    /**
     * Get the analytics for the wedding card.
     */
    public function analytics(): HasMany
    {
        return $this->hasMany(CardAnalytic::class);
    }

    /**
     * Get the RSVPs for the wedding card.
     */
    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    /**
     * Get the gifts for the wedding card.
     */
    public function gifts(): HasMany
    {
        return $this->hasMany(Gift::class);
    }

    /**
     * Scope to get only published cards.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get only active (non-expired) cards.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->whereNull('expiry_date')
                ->orWhere('expiry_date', '>', now());
        });
    }

    /**
     * Scope to get only expired cards.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expiry_date', '<=', now());
    }

    /**
     * Check if the card is expired.
     */
    public function isExpired(): bool
    {
        return (bool) ($this->expiry_date && $this->expiry_date->isPast());
    }

    /**
     * Get the full URL for viewing the card.
     */
    protected function viewUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => route('wedding-card.view', $this->unique_url),
        );
    }

    /**
     * Check if a user has reached their card limit.
     */
    public static function userHasReachedLimit(int $userId): bool
    {
        $maxCards = Setting::get('max_cards_per_user', 10);
        $userCardCount = static::where('user_id', $userId)->count();

        return $userCardCount >= $maxCards;
    }

    /**
     * Get remaining cards for a user.
     */
    public static function getRemainingCardsForUser(int $userId): int
    {
        $maxCards = Setting::get('max_cards_per_user', 10);
        $userCardCount = static::where('user_id', $userId)->count();

        return max(0, $maxCards - $userCardCount);
    }

    /**
     * Get the admin who approved this card.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to get only pending approval cards.
     */
    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->where('approval_status', ApprovalStatus::Pending->value);
    }

    /**
     * Scope to get only approved cards.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approval_status', ApprovalStatus::Approved->value);
    }

    /**
     * Scope to get only rejected cards.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('approval_status', ApprovalStatus::Rejected->value);
    }

    /**
     * Check if the card is pending approval.
     */
    public function isPending(): bool
    {
        return $this->approval_status === ApprovalStatus::Pending->value;
    }

    /**
     * Check if the card is approved.
     */
    public function isApproved(): bool
    {
        return $this->approval_status === ApprovalStatus::Approved->value;
    }

    /**
     * Check if the card is rejected.
     */
    public function isRejected(): bool
    {
        return $this->approval_status === ApprovalStatus::Rejected->value;
    }

    /**
     * Approve the card.
     */
    public function approve(int $adminId): void
    {
        $this->update([
            'approval_status' => ApprovalStatus::Approved->value,
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => null,
            'is_published' => true, // Auto-publish when approved
        ]);
    }

    /**
     * Reject the card.
     */
    public function reject(int $adminId, ?string $reason = null): void
    {
        $this->update([
            'approval_status' => ApprovalStatus::Rejected->value,
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => $reason,
            'is_published' => false,
        ]);
    }
}
