<?php

namespace App\Mcp\Tools;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetInventoryAlertsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get inventory alerts for products that need attention. Returns products with low stock, out of stock, and inactive products with remaining inventory.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        // Low stock products (below threshold but not zero)
        $lowStock = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
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
            ]);

        // Out of stock products
        $outOfStock = Product::where('stock_quantity', '=', 0)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => (float) $product->price,
                'alert_level' => 'out_of_stock',
            ]);

        // Inactive products with stock (potential waste)
        $inactiveWithStock = Product::where('is_active', false)
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
            ]);

        // Overdue invoices
        $overdueInvoices = Invoice::where('status', '!=', 'paid')
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
            ]);

        $data = [
            'alerts' => [
                'low_stock_count' => $lowStock->count(),
                'out_of_stock_count' => $outOfStock->count(),
                'inactive_with_stock_count' => $inactiveWithStock->count(),
                'overdue_invoices_count' => $overdueInvoices->count(),
            ],
            'low_stock_products' => $lowStock,
            'out_of_stock_products' => $outOfStock,
            'inactive_products_with_stock' => $inactiveWithStock,
            'overdue_invoices' => $overdueInvoices,
            'total_stock_value_at_risk' => round($inactiveWithStock->sum('value'), 2),
            'total_overdue_amount' => round($overdueInvoices->sum('amount'), 2),
            'timestamp' => now()->toISOString(),
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }
}
