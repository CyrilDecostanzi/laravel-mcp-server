<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['draft', 'sent', 'paid', 'overdue', 'cancelled']);
        $issueDate = fake()->dateTimeBetween('-6 months', 'now');
        $dueDate = fake()->dateTimeBetween($issueDate, '+30 days');
        $paidAt = $status === 'paid'
            ? fake()->dateTimeBetween($issueDate, $dueDate)
            : null;

        return [
            'invoice_number' => 'INV-'.strtoupper(fake()->unique()->bothify('####-????')),
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'status' => $status,
            'amount' => fake()->randomFloat(2, 50, 3000),
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'paid_at' => $paidAt,
        ];
    }
}
