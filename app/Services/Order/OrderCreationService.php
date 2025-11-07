<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderCreationService
{
    /**
     * Create a new order with validation.
     */
    public function createOrder(array $data): array
    {
        // Validate input
        $this->validateOrderData($data);

        try {
            DB::beginTransaction();

            // Get customer
            $customer = User::findOrFail($data['customer_id']);

            // Create order
            $order = new Order;
            $order->user_id = $customer->id;
            $order->status = $data['status'] ?? 'pending';
            $order->notes = $data['notes'] ?? null;

            // Calculate totals
            $subtotal = 0;
            $orderItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check stock availability
                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'stock' => "Insufficient stock for product: {$product->name}. Available: {$product->stock}, Requested: {$item['quantity']}",
                    ]);
                }

                $unitPrice = $item['unit_price'] ?? $product->price;
                $quantity = $item['quantity'];
                $itemTotal = $unitPrice * $quantity;

                $subtotal += $itemTotal;

                $orderItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $itemTotal,
                ];

                // Decrease stock if specified
                if ($data['decrease_stock'] ?? false) {
                    $product->stock -= $quantity;
                    $product->save();
                }
            }

            $order->subtotal = $subtotal;
            $order->tax = $subtotal * 0.20; // 20% VAT
            $order->total = $order->subtotal + $order->tax;
            $order->save();

            // Create order items
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            DB::commit();

            // Reload with relationships
            $order->load(['items.product', 'user']);

            return [
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => 'ORD-'.str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'customer' => [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'email' => $customer->email,
                    ],
                    'status' => $order->status,
                    'items' => $order->items->map(fn ($item) => [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'total' => (float) ($item->unit_price * $item->quantity),
                    ])->toArray(),
                    'subtotal' => round((float) $order->subtotal, 2),
                    'tax' => round((float) $order->tax, 2),
                    'total' => round((float) $order->total, 2),
                    'notes' => $order->notes,
                    'created_at' => $order->created_at->toISOString(),
                ],
                'message' => 'Order created successfully',
                'timestamp' => now()->toISOString(),
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(int $orderId, string $status): array
    {
        $validStatuses = ['pending', 'processing', 'completed', 'cancelled', 'failed'];

        if (! in_array($status, $validStatuses)) {
            return [
                'success' => false,
                'error' => 'Invalid status. Must be one of: '.implode(', ', $validStatuses),
                'timestamp' => now()->toISOString(),
            ];
        }

        try {
            $order = Order::findOrFail($orderId);
            $oldStatus = $order->status;
            $order->status = $status;
            $order->save();

            return [
                'success' => true,
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $order->status,
                'message' => "Order #{$order->id} status updated from '{$oldStatus}' to '{$status}'",
                'timestamp' => now()->toISOString(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ];
        }
    }

    /**
     * Validate order data.
     */
    private function validateOrderData(array $data): void
    {
        if (! isset($data['customer_id'])) {
            throw ValidationException::withMessages(['customer_id' => 'Customer ID is required']);
        }

        if (! isset($data['items']) || ! is_array($data['items']) || empty($data['items'])) {
            throw ValidationException::withMessages(['items' => 'Order must have at least one item']);
        }

        foreach ($data['items'] as $index => $item) {
            if (! isset($item['product_id'])) {
                throw ValidationException::withMessages(["items.{$index}.product_id" => 'Product ID is required']);
            }

            if (! isset($item['quantity']) || $item['quantity'] <= 0) {
                throw ValidationException::withMessages(["items.{$index}.quantity" => 'Quantity must be greater than 0']);
            }
        }
    }
}
