<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'completed', 'failed', 'refunded']);
        $paidAt = $status === 'completed'
            ? fake()->dateTimeBetween('-6 months', 'now')
            : null;
        
        return [
            'invoice_id' => Invoice::factory(),
            'order_id' => Order::factory(),
            'transaction_id' => 'TXN-' . strtoupper(fake()->unique()->bothify('########')),
            'payment_method' => fake()->randomElement(['credit_card', 'debit_card', 'paypal', 'bank_transfer', 'cash']),
            'status' => $status,
            'amount' => fake()->randomFloat(2, 10, 5000),
            'notes' => fake()->optional(0.3)->sentence(),
            'paid_at' => $paidAt,
        ];
    }
}
