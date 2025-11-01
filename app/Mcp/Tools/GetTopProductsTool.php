<?php

namespace App\Mcp\Tools;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetTopProductsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get top-performing products based on sales metrics. Returns best-selling products by quantity sold and revenue generated, with optional limit parameter.
    MARKDOWN;

    /**
     * Define the tool's input schema.
     */
    public function inputSchema(): JsonSchema
    {
        return new JsonSchema([
            'type' => 'object',
            'properties' => [
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Maximum number of products to return (default: 10, max: 50)',
                    'default' => 10,
                    'minimum' => 1,
                    'maximum' => 50,
                ],
                'sort_by' => [
                    'type' => 'string',
                    'description' => 'Sort by quantity or revenue',
                    'enum' => ['quantity', 'revenue'],
                    'default' => 'revenue',
                ],
            ],
        ]);
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $limit = min($request->params['limit'] ?? 10, 50);
        $sortBy = $request->params['sort_by'] ?? 'revenue';
        
        $orderColumn = $sortBy === 'quantity' ? 'total_quantity' : 'total_revenue';
        
        // Get top products with aggregated sales data
        $topProducts = OrderItem::select(
                'product_id',
                'product_name',
                'product_sku',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_price) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_id) as order_count'),
                DB::raw('AVG(unit_price) as avg_unit_price')
            )
            ->groupBy('product_id', 'product_name', 'product_sku')
            ->orderByDesc($orderColumn)
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $product = Product::find($item->product_id);
                
                return [
                    'product_id' => $item->product_id,
                    'name' => $item->product_name,
                    'sku' => $item->product_sku,
                    'metrics' => [
                        'total_quantity_sold' => (int) $item->total_quantity,
                        'total_revenue' => round((float) $item->total_revenue, 2),
                        'order_count' => (int) $item->order_count,
                        'average_unit_price' => round((float) $item->avg_unit_price, 2),
                    ],
                    'current_stock' => $product ? $product->stock_quantity : 'N/A',
                    'is_active' => $product ? $product->is_active : false,
                    'is_low_stock' => $product ? $product->isLowStock() : false,
                ];
            });

        $data = [
            'top_products' => $topProducts,
            'sort_by' => $sortBy,
            'limit' => $limit,
            'timestamp' => now()->toISOString(),
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }
}
