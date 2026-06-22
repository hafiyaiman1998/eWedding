<?php

namespace App\Models;

use App\Enums\GiftStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'wedding_card_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'amount',
        'currency',
        'bill_code',
        'bill_payment_id',
        'external_reference_no',
        'status',
        'payment_url',
        'message',
        'toyyibpay_response',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'toyyibpay_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function weddingCard(): BelongsTo
    {
        return $this->belongsTo(WeddingCard::class);
    }

    public function isPaid(): bool
    {
        return $this->status === GiftStatus::Paid->value;
    }

    public function isPending(): bool
    {
        return $this->status === GiftStatus::Pending->value;
    }

    public function isFailed(): bool
    {
        return $this->status === GiftStatus::Failed->value;
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => GiftStatus::Paid->value,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update([
            'status' => GiftStatus::Failed->value,
        ]);
    }

    public function generateExternalReference(): string
    {
        return 'GIFT-'.now()->format('YmdHis').'-'.$this->id;
    }

    public static function generateUniqueReference(): string
    {
        do {
            $reference = 'GIFT-'.now()->format('YmdHis').'-'.rand(1000, 9999);
        } while (static::where('external_reference_no', $reference)->exists());

        return $reference;
    }
}
