<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGiftRequest;
use App\Models\Gift;
use App\Models\WeddingCard;
use App\Services\Contracts\PaymentGatewayInterface;
use App\Services\GiftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class GiftController extends Controller
{
    public function __construct(
        protected PaymentGatewayInterface $paymentGateway,
        protected GiftService $giftService,
    ) {}

    /**
     * Create a new gift payment.
     */
    public function create(CreateGiftRequest $request): JsonResponse
    {
        try {
            $weddingCard = WeddingCard::findOrFail($request->wedding_card_id);

            $result = $this->giftService->createGiftBill($weddingCard, $request->validated());

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'payment_url' => $result['payment_url'],
                    'gift_id' => $result['gift_id'],
                    'bill_code' => $result['bill_code'],
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 400);
        } catch (\Exception $e) {
            Log::error('Gift creation failed', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while processing your gift. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle toyyibPay callback.
     */
    public function callback(Request $request): Response
    {
        Log::info('toyyibPay callback received', $request->all());

        try {
            if (! $this->paymentGateway->verifyCallback($request->all())) {
                Log::warning('Invalid toyyibPay callback', $request->all());

                return response('Invalid callback', 400);
            }

            $billCode = $request->input('billcode');
            $status = $request->input('status_id', $request->input('billpaymentStatus'));
            $externalRef = $request->input('external_reference_no', $request->input('billExternalReferenceNo'));

            if (! $billCode) {
                Log::warning('Missing bill code in callback', $request->all());

                return response('Missing bill code', 400);
            }

            $gift = Gift::where('bill_code', $billCode)
                ->orWhere('external_reference_no', $externalRef)
                ->first();

            if (! $gift) {
                Log::warning('Gift not found for callback', [
                    'bill_code' => $billCode,
                    'external_ref' => $externalRef,
                ]);

                return response('Gift not found', 404);
            }

            if ($status == '1' || $status == 'paid') {
                $gift->markAsPaid();
                $this->giftService->storePaymentDetails($gift);

                Log::info('Gift payment confirmed', [
                    'gift_id' => $gift->id,
                    'bill_code' => $billCode,
                    'amount' => $gift->amount,
                ]);
            } else {
                $gift->markAsFailed();
                Log::info('Gift payment failed', [
                    'gift_id' => $gift->id,
                    'bill_code' => $billCode,
                    'status' => $status,
                ]);
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('toyyibPay callback error', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response('Internal error', 500);
        }
    }

    /**
     * Handle return from toyyibPay payment page.
     */
    public function return(Request $request, Gift $gift): RedirectResponse
    {
        try {
            if ($gift->bill_code && $this->paymentGateway->isBillPaid($gift->bill_code)) {
                $this->giftService->reconcileStatus($gift);

                return redirect()->route('gift.receipt', ['gift' => $gift->id])
                    ->with('success', 'Thank you for your generous gift!');
            }

            return redirect()->route('gift.receipt', ['gift' => $gift->id])
                ->with('error', 'Payment was not completed. Please try again.');
        } catch (\Exception $e) {
            Log::error('Gift return handling error', [
                'gift_id' => $gift->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('gift.receipt', ['gift' => $gift->id])
                ->with('error', 'An error occurred while processing your payment.');
        }
    }

    /**
     * Display gift receipt.
     */
    public function receipt(Gift $gift): View
    {
        $gift->load('weddingCard');

        $details = $gift->weddingCard->card_details ?? [];

        return view('gift.receipt', compact('gift', 'details'));
    }

    /**
     * Check gift payment status (for AJAX polling).
     */
    public function status(Gift $gift): JsonResponse
    {
        try {
            if ($gift->isPending() && $gift->bill_code) {
                $this->giftService->reconcileStatus($gift);
            }

            return response()->json([
                'success' => true,
                'status' => $gift->status,
                'paid' => $gift->isPaid(),
                'amount' => $gift->amount,
                'currency' => $gift->currency,
                'paid_at' => $gift->paid_at?->toISOString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gift status check error', [
                'gift_id' => $gift->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unable to check payment status',
            ], 500);
        }
    }
}
