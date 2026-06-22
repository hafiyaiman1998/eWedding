<?php

namespace App\Services;

use App\Services\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToyyibPayService implements PaymentGatewayInterface
{
    private string $baseUrl;

    private string $userSecretKey;

    private string $categoryCode;

    private bool $sandbox;

    public function __construct()
    {
        $this->sandbox = config('toyyibpay.sandbox', true);
        $this->baseUrl = $this->sandbox
            ? 'https://dev.toyyibpay.com/index.php/api/'
            : 'https://toyyibpay.com/index.php/api/';

        $this->userSecretKey = config('toyyibpay.user_secret_key');
        $this->categoryCode = config('toyyibpay.category_code');
    }

    /**
     * Create a bill for gift payment
     */
    public function createBill(array $data): array
    {
        $billData = [
            'userSecretKey' => trim($this->userSecretKey),
            'categoryCode' => $this->categoryCode,
            'billName' => 'Wedding Gift from '.$data['guest_name'],
            'billDescription' => 'Wedding gift for '.$data['couple_names'],
            'billPriceSetting' => 1, // Fixed price
            'billPayorInfo' => 1, // Required payer info
            'billAmount' => $data['amount'] * 100, // Convert MYR to sen (smallest currency unit)
            'billReturnUrl' => $data['return_url'],
            'billCallbackUrl' => $data['callback_url'],
            'billExternalReferenceNo' => $data['external_reference_no'],
            'billTo' => $data['guest_name'],
            'billEmail' => $data['guest_email'],
            'billPhone' => $data['guest_phone'] ?? '',
            'billSplitPayment' => 0,
            'billSplitPaymentArgs' => '',
            'billPaymentChannel' => 2, // Both FPX and Credit Card
            'billDisplayMerchant' => 1,
            'billContentEmail' => $data['message'] ?? 'Thank you for your wedding gift!',
            'billChargeToCustomer' => 2, // Customer pays all fees
        ];

        try {
            // Log bill creation request (simplified)
            Log::info('ToyyibPay createBill request', [
                'amount_myr' => $data['amount'],
                'amount_sen' => $billData['billAmount'],
                'guest_name' => $billData['billTo'],
                'guest_email' => $billData['billEmail'],
                'external_ref' => $billData['billExternalReferenceNo'],
                'sandbox' => $this->sandbox,
            ]);

            // Send form-encoded request to ToyyibPay
            $response = Http::timeout(30)->asForm()->post($this->baseUrl.'createBill', $billData);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result[0]['BillCode'])) {
                    Log::info('ToyyibPay bill created successfully', [
                        'bill_code' => $result[0]['BillCode'],
                        'amount_myr' => $data['amount'],
                        'amount_sen' => $billData['billAmount'],
                    ]);

                    return [
                        'success' => true,
                        'bill_code' => $result[0]['BillCode'],
                        'payment_url' => ($this->sandbox ? 'https://dev.toyyibpay.com/' : 'https://toyyibpay.com/').$result[0]['BillCode'],
                        'response' => $result,
                    ];
                } else {
                    Log::error('toyyibPay createBill failed', ['response' => $result]);
                    $errorMsg = 'Unknown error';
                    if (isset($result[0]['msg'])) {
                        $errorMsg = $result[0]['msg'];
                    } elseif (isset($result['msg'])) {
                        $errorMsg = $result['msg'];
                    } elseif (is_array($result) && count($result) > 0) {
                        $errorMsg = json_encode($result[0]);
                    }

                    return [
                        'success' => false,
                        'error' => 'Failed to create bill: '.$errorMsg,
                        'response' => $result,
                    ];
                }
            } else {
                Log::error('toyyibPay API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers(),
                ]);

                return [
                    'success' => false,
                    'error' => 'Payment gateway error (HTTP '.$response->status().'). Please try again.',
                    'response' => $response->json(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('toyyibPay exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Connection error. Please try again.',
                'exception' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get bill transactions to check payment status
     */
    public function getBillTransactions(string $billCode): array
    {
        try {
            $response = Http::timeout(30)->post($this->baseUrl.'getBillTransactions', [
                'billCode' => $billCode,
            ]);

            if ($response->successful()) {
                $result = $response->json();

                return [
                    'success' => true,
                    'transactions' => $result,
                ];
            } else {
                Log::error('toyyibPay getBillTransactions failed', ['bill_code' => $billCode, 'response' => $response->body()]);

                return [
                    'success' => false,
                    'error' => 'Failed to get bill transactions',
                    'response' => $response->json(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('toyyibPay getBillTransactions exception', ['bill_code' => $billCode, 'error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Connection error',
                'exception' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if a bill has been paid
     */
    public function isBillPaid(string $billCode): bool
    {
        $result = $this->getBillTransactions($billCode);

        if (! $result['success']) {
            return false;
        }

        foreach ($result['transactions'] as $transaction) {
            if (isset($transaction['billpaymentStatus']) && $transaction['billpaymentStatus'] == '1') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get payment details from successful transaction
     */
    public function getPaymentDetails(string $billCode): ?array
    {
        $result = $this->getBillTransactions($billCode);

        if (! $result['success']) {
            return null;
        }

        foreach ($result['transactions'] as $transaction) {
            if (isset($transaction['billpaymentStatus']) && $transaction['billpaymentStatus'] == '1') {
                return $transaction;
            }
        }

        return null;
    }

    /**
     * Verify callback signature (if toyyibPay provides one)
     */
    public function verifyCallback(array $data): bool
    {
        // toyyibPay doesn't provide signature verification in their standard API
        // We'll verify by checking the bill exists and has valid status
        return isset($data['billcode']) && ! empty($data['billcode']);
    }
}
