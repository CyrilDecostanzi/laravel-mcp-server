<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting e-commerce database seeding...');

        // 1. Create users
        $this->command->info('👥 Creating 100 users...');
        $users = User::factory(100)->create();
        
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $this->command->info("✅ Admin created: {$admin->email}");

        // 2. Create categories
        $this->command->info('📁 Creating 10 categories...');
        $categories = Category::factory(10)->create();
        $this->command->info("✅ Created {$categories->count()} categories");

        // 3. Create products and attach to categories
        $this->command->info('📦 Creating 200 products...');
        $products = Product::factory(200)->create();
        
        // Attach 1-3 random categories to each product
        $products->each(function ($product) use ($categories) {
            $product->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
        $this->command->info("✅ Created {$products->count()} products with category associations");

        // 4. Create orders with items, invoices, and payments
        $this->command->info('🛒 Creating 500 orders with items, invoices, and payments...');
        
        $orders = Order::factory(500)
            ->recycle($users) // Reuse created users
            ->create()
            ->each(function ($order) use ($products) {
                // Create 1-5 order items per order
                $itemCount = rand(1, 5);
                $orderSubtotal = 0;
                
                for ($i = 0; $i < $itemCount; $i++) {
                    $product = $products->random();
                    $quantity = rand(1, 3);
                    $unitPrice = $product->price;
                    $totalPrice = $quantity * $unitPrice;
                    $orderSubtotal += $totalPrice;
                    
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ]);
                }
                
                // Update order totals
                $tax = $orderSubtotal * 0.20;
                $shipping = fake()->randomFloat(2, 5, 25);
                $total = $orderSubtotal + $tax + $shipping;
                
                $order->update([
                    'subtotal' => $orderSubtotal,
                    'tax' => $tax,
                    'shipping' => $shipping,
                    'total' => $total,
                ]);
                
                // Create invoice for completed/delivered orders
                if (in_array($order->status, ['delivered', 'shipped'])) {
                    $invoice = Invoice::create([
                        'invoice_number' => 'INV-' . strtoupper(fake()->unique()->bothify('####-????')),
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'status' => $order->status === 'delivered' ? 'paid' : 'sent',
                        'amount' => $total,
                        'issue_date' => $order->created_at,
                        'due_date' => $order->created_at->addDays(30),
                        'paid_at' => $order->status === 'delivered' ? $order->delivered_at : null,
                    ]);
                    
                    // Create payment for paid invoices
                    if ($invoice->status === 'paid') {
                        Payment::create([
                            'invoice_id' => $invoice->id,
                            'order_id' => $order->id,
                            'transaction_id' => 'TXN-' . strtoupper(fake()->unique()->bothify('########')),
                            'payment_method' => fake()->randomElement(['credit_card', 'debit_card', 'paypal', 'bank_transfer']),
                            'status' => 'completed',
                            'amount' => $total,
                            'paid_at' => $invoice->paid_at,
                        ]);
                    }
                }
            });

        $this->command->info("✅ Created {$orders->count()} orders");
        
        // Statistics
        $totalOrderItems = OrderItem::count();
        $totalInvoices = Invoice::count();
        $totalPayments = Payment::count();
        $totalRevenue = Order::sum('total');
        
        $this->command->newLine();
        $this->command->info('📊 Seeding Statistics:');
        $this->command->table(
            ['Metric', 'Count'],
            [
                ['Users', $users->count() + 1],
                ['Categories', $categories->count()],
                ['Products', $products->count()],
                ['Orders', $orders->count()],
                ['Order Items', $totalOrderItems],
                ['Invoices', $totalInvoices],
                ['Payments', $totalPayments],
                ['Total Revenue', '€' . number_format($totalRevenue, 2)],
            ]
        );
        
        $this->command->newLine();
        $this->command->info('✨ Database seeding completed successfully!');
    }
}
