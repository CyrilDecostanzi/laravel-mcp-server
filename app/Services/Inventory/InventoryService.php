<?php

namespace App\Services\Inventory;

use App\Models\Invoice;
use App\Models\Product;

class InventoryService
{
    /**
     * Get inventory alerts for products that need attention.
     */
    public function getInventoryAlerts(): array
    {
        $lowStock = $this->getLowStockProducts();
        $outOfStock = $this->getOutOfStockProducts();
        $inactiveWithStock = $this->getInactiveProductsWithStock();
        $overdueInvoices = $this->getOverdueInvoices();

        return [
            'alerts' => [
                'low_stock_count' => count($lowStock),
                'out_of_stock_count' => count($outOfStock),
                'inactive_with_stock_count' => count($inactiveWithStock),
                'overdue_invoices_count' => count($overdueInvoices),
            ],
            'low_stock_products' => $lowStock,
            'out_of_stock_products' => $outOfStock,
            'inactive_products_with_stock' => $inactiveWithStock,
            'overdue_invoices' => $overdueInvoices,
            'total_stock_value_at_risk' => round(array_sum(array_column($inactiveWithStock, 'value')), 2),
            'total_overdue_amount' => round(array_sum(array_column($overdueInvoices, 'amount')), 2),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Search products with inventory details.
     */
    public function searchProducts(string $query, int $limit = 20): array
    {
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('sku', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => (float) $product->price,
                'stock_quantity' => $product->stock_quantity,
                'low_stock_threshold' => $product->low_stock_threshold,
                'is_active' => $product->is_active,
                'category_id' => $product->category_id,
                'stock_status' => $this->getStockStatus($product),
            ]);

        return [
            'query' => $query,
            'total_results' => $products->count(),
            'limit' => $limit,
            'products' => $products->toArray(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get low stock products.
     */
    private function getLowStockProducts(): array
    {
        return Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->where('is_active', true)
            ->orderBy('stock_quantity', 'asc')
            ->get()
            ->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'current_stock' => $product->stock_quantity,
                'threshold' => $product->low_stock_threshold,
                'price' => (float) $product->price,
                'alert_level' => 'low_stock',
            ])
            ->toArray();
    }

    /**
     * Get out of stock products.
     */
    private function getOutOfStockProducts(): array
    {
        return Product::where('stock_quantity', '=', 0)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => (float) $product->price,
                'alert_level' => 'out_of_stock',
            ])
            ->toArray();
    }

    /**
     * Get inactive products with stock.
     */
    private function getInactiveProductsWithStock(): array
    {
        return Product::where('is_active', false)
            ->where('stock_quantity', '>', 0)
            ->orderByDesc('stock_quantity')
            ->get()
            ->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'stock_quantity' => $product->stock_quantity,
                'value' => round($product->stock_quantity * (float) $product->price, 2),
                'alert_level' => 'inactive_with_stock',
            ])
            ->toArray();
    }

    /**
     * Get overdue invoices.
     */
    private function getOverdueInvoices(): array
    {
        return Invoice::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->limit(20)
            ->get()
            ->map(fn($invoice) => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'amount' => (float) $invoice->amount,
                'due_date' => $invoice->due_date->toDateString(),
                'days_overdue' => $invoice->due_date->diffInDays(now()),
                'user_id' => $invoice->user_id,
                'status' => $invoice->status,
                'alert_level' => 'overdue_invoice',
            ])
            ->toArray();
    }

    /**
     * Determine stock status for a product.
     */
    private function getStockStatus(Product $product): string
    {
        if (!$product->is_active) {
            return 'inactive';
        }

        if ($product->stock_quantity === 0) {
            return 'out_of_stock';
        }

        if ($product->stock_quantity <= $product->low_stock_threshold) {
            return 'low_stock';
        }

        return 'in_stock';
    }
}
