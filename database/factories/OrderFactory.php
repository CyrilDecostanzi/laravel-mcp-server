<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']);
        $subtotal = fake()->randomFloat(2, 20, 2000);
        $tax = $subtotal * 0.20;
        $shipping = fake()->randomFloat(2, 5, 25);
        $total = $subtotal + $tax + $shipping;
        
        $createdAt = fake()->dateTimeBetween('-6 months', 'now');
        $shippedAt = in_array($status, ['shipped', 'delivered']) 
            ? fake()->dateTimeBetween($createdAt, 'now') 
            : null;
        $deliveredAt = $status === 'delivered' && $shippedAt
            ? fake()->dateTimeBetween($shippedAt, 'now')
            : null;
        
        return [
            'order_number' => 'ORD-' . strtoupper(fake()->unique()->bothify('####-????')),
            'user_id' => User::factory(),
            'status' => $status,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'shipping_address_line1' => fake()->streetAddress(),
            'shipping_address_line2' => fake()->optional(0.3)->secondaryAddress(),
            'shipping_city' => fake()->city(),
            'shipping_state' => fake()->state(),
            'shipping_postal_code' => fake()->postcode(),
            'shipping_country' => fake()->country(),
            'notes' => fake()->optional(0.2)->sentence(),
            'shipped_at' => $shippedAt,
            'delivered_at' => $deliveredAt,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
