<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use App\Models\WeddingCard;
use App\Services\ToyyibPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GiftController extends Controller
{
    protected ToyyibPayService $toyyibPayService;

    public function __construct(ToyyibPayService $toyyibPayService)
    {
        $this->toyyibPayService = $toyyibPayService;
    }

    /**
     * Create a new gift payment
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wedding_card_id' => 'required|exists:wedding_cards,id',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'amount' => 'required|numeric|min:1|max:10000',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $weddingCard = WeddingCard::findOrFail($request->wedding_card_id);
            
            // Create gift record
            $gift = Gift::create([
                'wedding_card_id' => $request->wedding_card_id,
                'guest_name' => $request->guest_name,
                'guest_email' => $request->guest_email,
                'guest_phone' => $request->guest_phone,
                'amount' => $request->amount,
                'message' => $request->message,
                'external_reference_no' => Gift::generateUniqueReference(),
                'status' => 'pending',
            ]);

            // Get couple names for bill description
            $coupleNames = 'the happy couple';
            if ($weddingCard->details) {
                $details = is_string($weddingCard->details) ? json_decode($weddingCard->details, true) : $weddingCard->details;
                $brideName = $details['bride_name'] ?? '';
                $groomName = $details['groom_name'] ?? '';
                if ($brideName && $groomName) {
                    $coupleNames = $groomName . ' & ' . $brideName;
                }
            }

            // Prepare toyyibPay bill data
            $billData = [
                'guest_name' => $gift->guest_name,
                'guest_email' => $gift->guest_email,
                'guest_phone' => $gift->guest_phone,
                'amount' => $gift->amount,
                'external_reference_no' => $gift->external_reference_no,
                'couple_names' => $coupleNames,
                'message' => $gift->message,
                'return_url' => route('gift.return', ['gift' => $gift->id]),
                'callback_url' => route('gift.callback'),
            ];

            // Create bill with toyyibPay
            $result = $this->toyyibPayService->createBill($billData);

            if ($result['success']) {
                // Update gift with bill information
                $gift->update([
                    'bill_code' => $result['bill_code'],
                    'payment_url' => $result['payment_url'],
                    'toyyibpay_response' => $result['response'],
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'payment_url' => $result['payment_url'],
                    'gift_id' => $gift->id,
                    'bill_code' => $result['bill_code'],
                ]);
            } else {
                DB::rollback();

                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollback();
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
     * Handle toyyibPay callback
     */
    public function callback(Request $request)
    {
        Log::info('toyyibPay callback received', $request->all());

        try {
            // Verify callback data
            if (!$this->toyyibPayService->verifyCallback($request->all())) {
                Log::warning('Invalid toyyibPay callback', $request->all());
                return response('Invalid callback', 400);
            }

            $billCode = $request->input('billcode');
            $status = $request->input('status_id', $request->input('billpaymentStatus'));
            $externalRef = $request->input('external_reference_no', $request->input('billExternalReferenceNo'));

            if (!$billCode) {
                Log::warning('Missing bill code in callback', $request->all());
                return response('Missing bill code', 400);
            }

            // Find gift by bill code or external reference
            $gift = Gift::where('bill_code', $billCode)
                ->orWhere('external_reference_no', $externalRef)
                ->first();

            if (!$gift) {
                Log::warning('Gift not found for callback', [
                    'bill_code' => $billCode,
                    'external_ref' => $externalRef,
                ]);
                return response('Gift not found', 404);
            }

            // Update gift status based on callback
            if ($status == '1' || $status == 'paid') {
                $gift->markAsPaid();
                
                // Get payment details from toyyibPay
                $paymentDetails = $this->toyyibPayService->getPaymentDetails($billCode);
                if ($paymentDetails) {
                    $currentResponse = $gift->toyyibpay_response ?? [];
                    $currentResponse['payment_details'] = $paymentDetails;
                    $gift->update(['toyyibpay_response' => $currentResponse]);
                }

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
     * Handle return from toyyibPay payment page
     */
    public function return(Request $request, Gift $gift)
    {
        try {
            // Check payment status with toyyibPay
            if ($gift->bill_code && $this->toyyibPayService->isBillPaid($gift->bill_code)) {
                if (!$gift->isPaid()) {
                    $gift->markAsPaid();
                    
                    // Get payment details
                    $paymentDetails = $this->toyyibPayService->getPaymentDetails($gift->bill_code);
                    if ($paymentDetails) {
                        $currentResponse = $gift->toyyibpay_response ?? [];
                        $currentResponse['payment_details'] = $paymentDetails;
                        $gift->update(['toyyibpay_response' => $currentResponse]);
                    }
                }
                
                return redirect()->route('gift.receipt', ['gift' => $gift->id])
                    ->with('success', 'Thank you for your generous gift!');
            } else {
                return redirect()->route('gift.receipt', ['gift' => $gift->id])
                    ->with('error', 'Payment was not completed. Please try again.');
            }

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
     * Display gift receipt
     */
    public function receipt(Gift $gift)
    {
        $gift->load('weddingCard');
        
        // Get couple details
        $details = [];
        if ($gift->weddingCard->details) {
            $details = is_string($gift->weddingCard->details) ? 
                json_decode($gift->weddingCard->details, true) : 
                $gift->weddingCard->details;
        }

        return view('gift.receipt', compact('gift', 'details'));
    }

    /**
     * Check gift payment status (for AJAX polling)
     */
    public function status(Gift $gift)
    {
        try {
            // Check with toyyibPay if not already paid
            if ($gift->isPending() && $gift->bill_code) {
                if ($this->toyyibPayService->isBillPaid($gift->bill_code)) {
                    $gift->markAsPaid();
                    
                    // Get payment details
                    $paymentDetails = $this->toyyibPayService->getPaymentDetails($gift->bill_code);
                    if ($paymentDetails) {
                        $currentResponse = $gift->toyyibpay_response ?? [];
                        $currentResponse['payment_details'] = $paymentDetails;
                        $gift->update(['toyyibpay_response' => $currentResponse]);
                    }
                }
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
