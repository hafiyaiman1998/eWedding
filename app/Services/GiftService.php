<?php

namespace App\Services;

use App\Models\Gift;
use App\Models\WeddingCard;
use App\Services\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\DB;

class GiftService
{
    public function __construct(protected PaymentGatewayInterface $paymentGateway) {}

    /**
     * Create a gift record and a payment bill for it.
     *
     * @param  array{wedding_card_id: int, guest_name: string, guest_email: string, guest_phone?: ?string, amount: int|float, message?: ?string}  $validated
     * @return array{success: bool, payment_url?: string, gift_id?: int, bill_code?: string, error?: string}
     */
    public function createGiftBill(WeddingCard $weddingCard, array $validated): array
    {
        return DB::transaction(function () use ($weddingCard, $validated): array {
            $gift = Gift::create([
                'wedding_card_id' => $validated['wedding_card_id'],
                'guest_name' => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
                'guest_phone' => $validated['guest_phone'] ?? null,
                'amount' => $validated['amount'],
                'message' => $validated['message'] ?? null,
                'external_reference_no' => Gift::generateUniqueReference(),
                'status' => 'pending',
            ]);

            $billData = [
                'guest_name' => $gift->guest_name,
                'guest_email' => $gift->guest_email,
                'guest_phone' => $gift->guest_phone,
                'amount' => $gift->amount,
                'external_reference_no' => $gift->external_reference_no,
                'couple_names' => $this->resolveCoupleNames($weddingCard),
                'message' => $gift->message,
                'return_url' => route('gift.return', ['gift' => $gift->id]),
                'callback_url' => route('gift.callback'),
            ];

            $result = $this->paymentGateway->createBill($billData);

            if ($result['success']) {
                $gift->update([
                    'bill_code' => $result['bill_code'],
                    'payment_url' => $result['payment_url'],
                    'toyyibpay_response' => $result['response'],
                ]);

                return [
                    'success' => true,
                    'payment_url' => $result['payment_url'],
                    'gift_id' => $gift->id,
                    'bill_code' => $result['bill_code'],
                ];
            }

            DB::rollBack();

            return [
                'success' => false,
                'error' => $result['error'],
            ];
        });
    }

    /**
     * Reconcile a gift's status against the payment gateway and persist any payment details.
     */
    public function reconcileStatus(Gift $gift): Gift
    {
        if ($gift->bill_code && $this->paymentGateway->isBillPaid($gift->bill_code)) {
            if (! $gift->isPaid()) {
                $gift->markAsPaid();
                $this->storePaymentDetails($gift);
            }
        }

        return $gift;
    }

    /**
     * Derive the couple name from a wedding card's card_details.
     */
    public function resolveCoupleNames(WeddingCard $weddingCard): string
    {
        $details = $weddingCard->card_details ?? [];

        $brideName = $details['bride_name'] ?? '';
        $groomName = $details['groom_name'] ?? '';

        if ($brideName && $groomName) {
            return $groomName.' & '.$brideName;
        }

        return 'the happy couple';
    }

    /**
     * Fetch the latest payment details from the gateway and merge them into the stored response.
     */
    public function storePaymentDetails(Gift $gift): void
    {
        if (! $gift->bill_code) {
            return;
        }

        $paymentDetails = $this->paymentGateway->getPaymentDetails($gift->bill_code);

        if ($paymentDetails) {
            $currentResponse = $gift->toyyibpay_response ?? [];
            $currentResponse['payment_details'] = $paymentDetails;
            $gift->update(['toyyibpay_response' => $currentResponse]);
        }
    }
}
