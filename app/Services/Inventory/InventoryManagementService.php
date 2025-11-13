<?php

namespace App\Services\Inventory;

use App\Models\Product;
use Illuminate\Validation\ValidationException;

class InventoryManagementService
{
    /**
     * Update product stock.
     */
    public function updateStock(int $productId, int $quantity, string $operation = 'set'): array
    {
        try {
            $product = Product::findOrFail($productId);
            $oldStock = $product->stock_quantity;

            switch ($operation) {
                case 'set':
                    $product->stock_quantity = max(0, $quantity);
                    break;
                case 'add':
                    $product->stock_quantity += $quantity;
                    break;
                case 'subtract':
                    $product->stock_quantity = max(0, $product->stock_quantity - $quantity);
                    break;
                default:
                    throw ValidationException::withMessages([
                        'operation' => "Invalid operation. Must be 'set', 'add', or 'subtract'",
                    ]);
            }

            $product->save();

            $stockStatus = $this->getStockStatus($product->stock_quantity);

            return [
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'old_stock' => $oldStock,
                    'new_stock' => $product->stock_quantity,
                    'stock_change' => $product->stock_quantity - $oldStock,
                    'stock_status' => $stockStatus,
                ],
                'message' => "Stock updated for '{$product->name}': {$oldStock} → {$product->stock_quantity}",
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
     * Bulk update stock for multiple products.
     */
    public function bulkUpdateStock(array $updates): array
    {
        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($updates as $update) {
            $result = $this->updateStock(
                $update['product_id'],
                $update['quantity'],
                $update['operation'] ?? 'set'
            );

            if ($result['success']) {
                $successCount++;
            } else {
                $errorCount++;
            }

            $results[] = $result;
        }

        return [
            'total_processed' => count($updates),
            'successful' => $successCount,
            'failed' => $errorCount,
            'results' => $results,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Update product price.
     */
    public function updatePrice(int $productId, float $newPrice): array
    {
        try {
            if ($newPrice < 0) {
                throw ValidationException::withMessages([
                    'price' => 'Price cannot be negative',
                ]);
            }

            $product = Product::findOrFail($productId);
            $oldPrice = $product->price;
            $product->price = $newPrice;
            $product->save();

            $priceChange = (($newPrice - $oldPrice) / $oldPrice) * 100;

            return [
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'old_price' => round((float) $oldPrice, 2),
                    'new_price' => round((float) $newPrice, 2),
                    'price_change_percentage' => round($priceChange, 2),
                ],
                'message' => "Price updated for '{$product->name}': €{$oldPrice} → €{$newPrice}",
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
     * Apply discount to product.
     */
    public function applyDiscount(int $productId, float $discountPercentage): array
    {
        try {
            if ($discountPercentage < 0 || $discountPercentage > 100) {
                throw ValidationException::withMessages([
                    'discount' => 'Discount must be between 0 and 100',
                ]);
            }

            $product = Product::findOrFail($productId);
            $oldPrice = $product->price;
            $discountAmount = $oldPrice * ($discountPercentage / 100);
            $newPrice = $oldPrice - $discountAmount;

            $product->price = $newPrice;
            $product->save();

            return [
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'original_price' => round((float) $oldPrice, 2),
                    'discount_percentage' => $discountPercentage,
                    'discount_amount' => round($discountAmount, 2),
                    'new_price' => round((float) $newPrice, 2),
                    'savings' => round($discountAmount, 2),
                ],
                'message' => "{$discountPercentage}% discount applied to '{$product->name}': €{$oldPrice} → €{$newPrice}",
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
     * Create a new product.
     */
    public function createProduct(array $data): array
    {
        try {
            // Validate required fields
            if (! isset($data['name']) || empty($data['name'])) {
                throw ValidationException::withMessages(['name' => 'Product name is required']);
            }

            if (! isset($data['price']) || $data['price'] < 0) {
                throw ValidationException::withMessages(['price' => 'Valid price is required']);
            }

            // Generate SKU if not provided
            $sku = $data['sku'] ?? $this->generateSKU($data['name']);

            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'sku' => $sku,
                'price' => $data['price'],
                'stock' => $data['stock'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
            ]);

            return [
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => round((float) $product->price, 2),
                    'stock_quantity' => $product->stock_quantity,
                    'is_active' => $product->is_active,
                    'created_at' => $product->created_at->toISOString(),
                ],
                'message' => "Product '{$product->name}' created successfully",
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
     * Get stock status based on quantity.
     */
    private function getStockStatus(int $stock): string
    {
        if ($stock === 0) {
            return 'Out of Stock';
        } elseif ($stock < 10) {
            return 'Low Stock';
        } elseif ($stock < 50) {
            return 'Normal';
        }

        return 'In Stock';
    }

    /**
     * Generate SKU from product name.
     */
    private function generateSKU(string $name): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 3));
        $random = strtoupper(substr(md5(uniqid()), 0, 6));

        return $prefix.'-'.$random;
    }
}
