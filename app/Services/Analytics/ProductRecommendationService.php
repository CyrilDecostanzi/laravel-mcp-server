<?php

namespace App\Services\Analytics;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProductRecommendationService
{
    /**
     * Get product recommendations for a customer based on purchase history.
     */
    public function getRecommendationsForCustomer(int $customerId, int $limit = 5): array
    {
        $customer = User::find($customerId);

        if (! $customer) {
            return [
                'error' => 'Customer not found',
                'timestamp' => now()->toISOString(),
            ];
        }

        // Get products the customer has already purchased
        $purchasedProductIds = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', $customerId)
            ->where('orders.status', 'completed')
            ->pluck('order_items.product_id')
            ->unique()
            ->toArray();

        // Find frequently bought together products
        $recommendations = $this->getFrequentlyBoughtTogether($purchasedProductIds, $limit);

        // If not enough recommendations, add top-selling products
        if (count($recommendations) < $limit) {
            $topProducts = $this->getTopSellingProducts($limit - count($recommendations), $purchasedProductIds);
            $recommendations = array_merge($recommendations, $topProducts);
        }

        return [
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'purchased_products_count' => count($purchasedProductIds),
            'recommendations' => $recommendations,
            'recommendation_strategy' => 'Collaborative filtering + Top sellers',
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get cross-sell recommendations for a specific product.
     */
    public function getCrossSellProducts(int $productId, int $limit = 5): array
    {
        $product = Product::find($productId);

        if (! $product) {
            return [
                'error' => 'Product not found',
                'timestamp' => now()->toISOString(),
            ];
        }

        // Find products frequently bought together with this product
        $crossSells = DB::table('order_items as oi1')
            ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
            ->join('products', 'oi2.product_id', '=', 'products.id')
            ->where('oi1.product_id', $productId)
            ->where('oi2.product_id', '!=', $productId)
            ->where('products.is_active', true)
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.price',
                'products.stock',
                DB::raw('COUNT(*) as times_bought_together')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.price', 'products.stock')
            ->orderByDesc('times_bought_together')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'product_id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'price' => round((float) $item->price, 2),
                'stock' => $item->stock,
                'times_bought_together' => (int) $item->times_bought_together,
                'confidence_score' => min(100, ((int) $item->times_bought_together) * 10),
            ])
            ->toArray();

        return [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => round((float) $product->price, 2),
            ],
            'cross_sell_recommendations' => $crossSells,
            'total_recommendations' => count($crossSells),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get upsell recommendations (higher-priced alternatives).
     */
    public function getUpsellProducts(int $productId, int $limit = 5): array
    {
        $product = Product::find($productId);

        if (! $product) {
            return [
                'error' => 'Product not found',
                'timestamp' => now()->toISOString(),
            ];
        }

        // Find products in the same category with higher price
        $upsells = Product::where('is_active', true)
            ->where('id', '!=', $productId)
            ->where('price', '>', $product->price)
            ->where('price', '<=', $product->price * 1.5) // Max 50% more expensive
            ->orderBy('price', 'asc')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'product_id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'price' => round((float) $item->price, 2),
                'price_difference' => round((float) ($item->price - $product->price), 2),
                'price_increase_percentage' => round((($item->price - $product->price) / $product->price) * 100, 1),
                'stock' => $item->stock,
            ])
            ->toArray();

        return [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => round((float) $product->price, 2),
            ],
            'upsell_recommendations' => $upsells,
            'total_recommendations' => count($upsells),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get trending products based on recent sales.
     */
    public function getTrendingProducts(int $days = 7, int $limit = 10): array
    {
        $trending = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.created_at', '>=', now()->subDays($days))
            ->where('orders.status', 'completed')
            ->where('products.is_active', true)
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.price',
                'products.stock',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.price', 'products.stock')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'product_id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'price' => round((float) $item->price, 2),
                'stock' => $item->stock,
                'units_sold' => (int) $item->total_sold,
                'orders_count' => (int) $item->orders_count,
                'revenue' => round((float) $item->revenue, 2),
                'trend_score' => (int) $item->total_sold,
            ])
            ->toArray();

        return [
            'period' => "Last {$days} days",
            'trending_products' => $trending,
            'total_products' => count($trending),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get frequently bought together products.
     */
    private function getFrequentlyBoughtTogether(array $productIds, int $limit): array
    {
        if (empty($productIds)) {
            return [];
        }

        return DB::table('order_items as oi1')
            ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
            ->join('products', 'oi2.product_id', '=', 'products.id')
            ->whereIn('oi1.product_id', $productIds)
            ->whereNotIn('oi2.product_id', $productIds)
            ->where('products.is_active', true)
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.price',
                'products.stock',
                DB::raw('COUNT(*) as frequency')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.price', 'products.stock')
            ->orderByDesc('frequency')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'product_id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'price' => round((float) $item->price, 2),
                'stock' => $item->stock,
                'recommendation_score' => (int) $item->frequency,
                'reason' => 'Frequently bought with your purchases',
            ])
            ->toArray();
    }

    /**
     * Get top-selling products.
     */
    private function getTopSellingProducts(int $limit, array $excludeIds = []): array
    {
        return Product::select('products.*')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->whereNotIn('products.id', $excludeIds)
            ->where('products.is_active', true)
            ->groupBy('products.id')
            ->orderByRaw('SUM(order_items.quantity) DESC')
            ->limit($limit)
            ->get()
            ->map(fn ($product) => [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => round((float) $product->price, 2),
                'stock' => $product->stock,
                'recommendation_score' => 75,
                'reason' => 'Popular product',
            ])
            ->toArray();
    }
}
