<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(rand(2, 4), true);
        $price = fake()->randomFloat(2, 5, 500);
        $costPrice = $price * fake()->randomFloat(2, 0.4, 0.7);

        return [
            'name' => ucwords($name),
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => fake()->optional(0.8)->paragraph(),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####-???')),
            'price' => $price,
            'cost_price' => $costPrice,
            'stock_quantity' => fake()->numberBetween(0, 500),
            'low_stock_threshold' => fake()->numberBetween(5, 20),
            'is_active' => fake()->boolean(85),
            'image_url' => fake()->optional(0.6)->imageUrl(640, 480, 'products', true),
        ];
    }
}
