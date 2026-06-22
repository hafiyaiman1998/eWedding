<?php

namespace App\Services\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Create a bill for gift payment.
     *
     * @param  array{guest_name: string, guest_email: string, guest_phone?: ?string, amount: int|float, external_reference_no: string, couple_names: string, message?: ?string, return_url: string, callback_url: string}  $data
     * @return array{success: bool, bill_code?: string, payment_url?: string, response?: mixed, error?: string, exception?: string}
     */
    public function createBill(array $data): array;

    /**
     * Get bill transactions to check payment status.
     *
     * @return array{success: bool, transactions?: mixed, error?: string, response?: mixed, exception?: string}
     */
    public function getBillTransactions(string $billCode): array;

    /**
     * Check if a bill has been paid.
     */
    public function isBillPaid(string $billCode): bool;

    /**
     * Get payment details from a successful transaction.
     *
     * @return array<string, mixed>|null
     */
    public function getPaymentDetails(string $billCode): ?array;

    /**
     * Verify callback data from the payment gateway.
     *
     * @param  array<string, mixed>  $data
     */
    public function verifyCallback(array $data): bool;
}
