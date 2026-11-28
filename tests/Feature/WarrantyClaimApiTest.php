<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarrantyClaimApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_claim_for_owned_order_and_read_it_back(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'CUS-TEST-001', 'name' => $user->name, 'email' => $user->email,
            'phone' => '081234567890', 'company' => 'Test Co',
        ]);
        $order = Order::create([
            'customer_id' => $customer->id, 'order_code' => 'ORD-TEST-001',
            'serial_number' => 'SN-TEST-001', 'warranty_period_months' => 24,
            'total_amount' => 100000, 'status' => Order::STATUS_LUNAS,
        ]);
        $token = $user->createToken('test')->plainTextToken;
        $payload = [
            'order_id' => $order->id, 'serial_number' => $order->serial_number,
            'purchase_date' => now()->toDateString(), 'damage_description' => 'Mesin tidak menyala.',
        ];

        $this->withToken($token)->postJson('/api/services/warranty-claims', $payload)
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'dalam_peninjauan');

        $this->assertDatabaseHas('warranty_claims', [
            'user_id' => $user->id, 'customer_id' => $customer->id, 'order_id' => $order->id,
            'serial_number' => $order->serial_number, 'status' => 'dalam_peninjauan',
            'verification_status' => 'menunggu_verifikasi',
        ]);

        $this->withToken($token)->getJson('/api/services/warranty-claims')
            ->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_claim_rejects_another_customers_order(): void
    {
        $user = User::factory()->create();
        $other = Customer::create([
            'customer_code' => 'CUS-TEST-002', 'name' => 'Other', 'email' => 'other@example.test',
            'phone' => '081234567891',
        ]);
        $order = Order::create([
            'customer_id' => $other->id, 'order_code' => 'ORD-TEST-002',
            'serial_number' => 'SN-TEST-002', 'warranty_period_months' => 24,
            'total_amount' => 100000, 'status' => Order::STATUS_LUNAS,
        ]);

        $this->withToken($user->createToken('test')->plainTextToken)
            ->postJson('/api/services/warranty-claims', [
                'order_id' => $order->id, 'serial_number' => $order->serial_number,
                'purchase_date' => now()->toDateString(), 'damage_description' => 'Rusak.',
            ])->assertUnprocessable();
    }

    public function test_customer_can_submit_new_claim_after_previous_claim_is_completed(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create([
            'customer_code' => 'CUS-TEST-003', 'name' => $user->name, 'email' => $user->email,
            'phone' => '081234567892', 'company' => 'Test Co',
        ]);
        $order = Order::create([
            'customer_id' => $customer->id, 'order_code' => 'ORD-TEST-003',
            'serial_number' => 'SN-TEST-003', 'warranty_period_months' => 24,
            'total_amount' => 100000, 'status' => Order::STATUS_LUNAS,
        ]);
        \App\Models\WarrantyClaim::create([
            'user_id' => $user->id, 'customer_id' => $customer->id, 'order_id' => $order->id,
            'claim_code' => 'CLM-TEST-DONE', 'serial_number' => $order->serial_number,
            'purchase_date' => now()->toDateString(), 'damage_description' => 'Klaim sebelumnya.',
            'status' => \App\Models\WarrantyClaim::STATUS_SELESAI,
            'verification_status' => \App\Models\WarrantyClaim::VERIFIKASI_VALID,
        ]);

        $this->withToken($user->createToken('test')->plainTextToken)
            ->postJson('/api/services/warranty-claims', [
                'order_id' => $order->id, 'serial_number' => $order->serial_number,
                'purchase_date' => now()->toDateString(), 'damage_description' => 'Kerusakan baru.',
            ])->assertCreated();
    }
}
