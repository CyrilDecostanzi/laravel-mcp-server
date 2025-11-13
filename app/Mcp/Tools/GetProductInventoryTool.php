<?php

namespace App\Mcp\Tools;

use App\Models\Product;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetProductInventoryTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Search and filter products with inventory details. Supports filtering by stock status, category, price range, and search query. Returns product details with stock levels and sales performance.
    MARKDOWN;

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()
                ->description('Search query (product name or SKU)'),
            'stock_status' => $schema->string()
                ->description('Filter by stock status')
                ->enum(['all', 'in_stock', 'low_stock', 'out_of_stock'])
                ->default('all'),
            'is_active' => $schema->boolean()
                ->description('Filter by active status'),
            'min_price' => $schema->number()
                ->description('Minimum product price'),
            'max_price' => $schema->number()
                ->description('Maximum product price'),
            'category_id' => $schema->integer()
                ->description('Filter by category ID'),
            'limit' => $schema->integer()
                ->description('Maximum number of results (default: 50, max: 200)')
                ->default(50),
        ];
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $params = $request->validate([
            'query' => 'string',
            'stock_status' => 'string|in:all,in_stock,low_stock,out_of_stock',
            'is_active' => 'boolean',
            'min_price' => 'numeric',
            'max_price' => 'numeric',
            'category_id' => 'integer',
            'limit' => 'integer|min:1|max:200',
        ]);

        $query = Product::with(['categories', 'orderItems']);

        // Text search
        if (! empty($params['query'])) {
            $searchTerm = $params['query'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('sku', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Stock status filter
        $stockStatus = $params['stock_status'] ?? 'all';
        if ($stockStatus === 'low_stock') {
            $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                ->where('stock_quantity', '>', 0);
        } elseif ($stockStatus === 'out_of_stock') {
            $query->where('stock_quantity', 0);
        } elseif ($stockStatus === 'in_stock') {
            $query->whereColumn('stock_quantity', '>', 'low_stock_threshold');
        }

        // Active status filter
        if (isset($params['is_active'])) {
            $query->where('is_active', $params['is_active']);
        }

        // Price range filter
        if (! empty($params['min_price'])) {
            $query->where('price', '>=', $params['min_price']);
        }
        if (! empty($params['max_price'])) {
            $query->where('price', '<=', $params['max_price']);
        }

        // Category filter
        if (! empty($params['category_id'])) {
            $query->whereHas('categories', function ($q) use ($params) {
                $q->where('categories.id', $params['category_id']);
            });
        }

        $limit = min($params['limit'] ?? 50, 200);

        $products = $query->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                $totalSold = $product->orderItems->sum('quantity');
                $totalRevenue = $product->orderItems->sum('total_price');

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'description' => $product->description,
                    'pricing' => [
                        'price' => (float) $product->price,
                        'cost_price' => (float) $product->cost_price,
                        'margin' => round(((float) $product->price - (float) $product->cost_price) / (float) $product->price * 100, 2),
                    ],
                    'inventory' => [
                        'stock_quantity' => $product->stock_quantity,
                        'low_stock_threshold' => $product->low_stock_threshold,
                        'stock_status' => $product->isOutOfStock() ? 'out_of_stock'
                            : ($product->isLowStock() ? 'low_stock' : 'in_stock'),
                        'stock_value' => round($product->stock_quantity * (float) $product->price, 2),
                    ],
                    'sales_performance' => [
                        'total_units_sold' => (int) $totalSold,
                        'total_revenue' => round((float) $totalRevenue, 2),
                    ],
                    'categories' => $product->categories->pluck('name'),
                    'is_active' => $product->is_active,
                    'image_url' => $product->image_url,
                    'created_at' => $product->created_at->toISOString(),
                ];
            });

        $data = [
            'products' => $products,
            'count' => $products->count(),
            'summary' => [
                'total_stock_value' => round($products->sum('inventory.stock_value'), 2),
                'low_stock_count' => $products->where('inventory.stock_status', 'low_stock')->count(),
                'out_of_stock_count' => $products->where('inventory.stock_status', 'out_of_stock')->count(),
            ],
            'filters_applied' => array_filter($params ?? []),
            'timestamp' => now()->toISOString(),
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }
}
