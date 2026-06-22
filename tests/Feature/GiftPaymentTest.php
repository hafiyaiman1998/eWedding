<?php

namespace Tests\Feature;

use App\Models\Gift;
use App\Models\WeddingCard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GiftPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_gift_creates_pending_record_and_returns_payment_url(): void
    {
        // Stub the toyyibPay createBill endpoint.
        Http::fake([
            '*createBill*' => Http::response([['BillCode' => 'abc123']], 200),
        ]);

        $card = WeddingCard::factory()->published()->create();

        $response = $this->postJson(route('gift.create'), [
            'wedding_card_id' => $card->id,
            'guest_name' => 'Generous Guest',
            'guest_email' => 'gift@example.com',
            'amount' => 50,
            'message' => 'Congrats!',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonPath('bill_code', 'abc123');

        $gift = Gift::where('wedding_card_id', $card->id)->first();
        $this->assertNotNull($gift);

        // characterizes current behavior: the gift is created with status
        // 'pending' (it only becomes 'paid' via callback/return/status flow).
        $this->assertSame('pending', $gift->status);
        $this->assertSame('abc123', $gift->bill_code);
        $this->assertNotNull($gift->payment_url);
        $this->assertStringContainsString('abc123', $gift->payment_url);
    }

    public function test_create_gift_handles_bill_creation_failure(): void
    {
        // toyyibPay returns no BillCode -> service reports failure.
        Http::fake([
            '*createBill*' => Http::response([['msg' => 'invalid category']], 200),
        ]);

        $card = WeddingCard::factory()->published()->create();

        $response = $this->postJson(route('gift.create'), [
            'wedding_card_id' => $card->id,
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'amount' => 25,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);

        // characterizes current behavior: on bill failure the DB transaction
        // is rolled back, so no gift row remains.
        $this->assertDatabaseCount('gifts', 0);
    }

    public function test_create_gift_validation_error_returns_422(): void
    {
        $response = $this->postJson(route('gift.create'), [
            'wedding_card_id' => 999999,
            'guest_name' => '',
            'amount' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_callback_marks_gift_as_paid_on_success_status(): void
    {
        // getBillTransactions (called by getPaymentDetails) returns a paid txn.
        Http::fake([
            '*getBillTransactions*' => Http::response([
                ['billpaymentStatus' => '1'],
            ], 200),
        ]);

        $gift = Gift::factory()->create([
            'status' => 'pending',
            'bill_code' => 'PAIDBILL',
        ]);

        $response = $this->post(route('gift.callback'), [
            'billcode' => 'PAIDBILL',
            'status_id' => '1',
            'external_reference_no' => $gift->external_reference_no,
        ]);

        $response->assertOk();
        $this->assertTrue($gift->fresh()->isPaid());
        $this->assertNotNull($gift->fresh()->paid_at);
    }

    public function test_callback_marks_gift_as_failed_on_non_success_status(): void
    {
        $gift = Gift::factory()->create([
            'status' => 'pending',
            'bill_code' => 'FAILBILL',
        ]);

        $response = $this->post(route('gift.callback'), [
            'billcode' => 'FAILBILL',
            'status_id' => '3',
            'external_reference_no' => $gift->external_reference_no,
        ]);

        $response->assertOk();
        $this->assertSame('failed', $gift->fresh()->status);
    }
}
