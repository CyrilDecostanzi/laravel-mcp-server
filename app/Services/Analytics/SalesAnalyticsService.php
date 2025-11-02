<?php

namespace App\Services\Analytics;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsService
{
    /**
     * Get comprehensive sales statistics.
     */
    public function getSalesStats(): array
    {
        // Overall statistics
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Orders by status
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'), DB::raw('sum(total) as revenue'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->status => [
                    'count' => $item->count,
                    'revenue' => (float) $item->revenue,
                ]
            ]);

        // Time-based statistics
        $today = $this->getOrdersForPeriod('today');
        $thisWeek = $this->getOrdersForPeriod('this_week');
        $thisMonth = $this->getOrdersForPeriod('this_month');
        $lastMonth = $this->getOrdersForPeriod('last_month');

        // Payment statistics
        $paymentStats = $this->getPaymentStatistics();

        // Growth metrics
        $growthRate = $lastMonth['revenue'] > 0
            ? (($thisMonth['revenue'] - $lastMonth['revenue']) / $lastMonth['revenue']) * 100
            : 0;

        return [
            'overview' => [
                'total_orders' => $totalOrders,
                'total_revenue' => round((float) $totalRevenue, 2),
                'average_order_value' => round($averageOrderValue, 2),
                'completed_payments' => $paymentStats['completed'],
                'total_payment_amount' => $paymentStats['total_amount'],
            ],
            'orders_by_status' => $ordersByStatus,
            'time_periods' => [
                'today' => $today,
                'this_week' => $thisWeek,
                'this_month' => $thisMonth,
                'last_month' => $lastMonth,
            ],
            'growth' => [
                'monthly_growth_rate' => round($growthRate, 2) . '%',
            ],
            'payments' => [
                'completed' => $paymentStats['completed'],
                'pending' => $paymentStats['pending'],
                'failed' => $paymentStats['failed'],
                'by_method' => $paymentStats['by_method'],
            ],
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get revenue breakdown by period.
     */
    public function getRevenueByPeriod(string $period = 'daily', int $limit = 30): array
    {
        $groupFormat = match ($period) {
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $revenues = Order::select(
            DB::raw("DATE_FORMAT(created_at, '{$groupFormat}') as period"),
            DB::raw('COUNT(*) as orders'),
            DB::raw('SUM(total) as revenue'),
            DB::raw('AVG(total) as avg_order_value')
        )
            ->groupBy('period')
            ->orderBy('period', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'period' => $item->period,
                'orders' => (int) $item->orders,
                'revenue' => round((float) $item->revenue, 2),
                'average_order_value' => round((float) $item->avg_order_value, 2),
            ]);

        return [
            'period_type' => $period,
            'data' => $revenues->toArray(),
            'total_periods' => $revenues->count(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get top products by quantity or revenue.
     */
    public function getTopProducts(int $limit = 10, string $by = 'revenue'): array
    {
        $orderBy = $by === 'quantity' ? 'total_quantity' : 'total_revenue';

        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.unit_price * order_items.quantity) as total_revenue'),
                DB::raw('COUNT(DISTINCT order_items.order_id) as orders_count')
            )
            ->groupBy('products.id', 'products.name', 'products.sku', 'products.price')
            ->orderByDesc($orderBy)
            ->limit($limit)
            ->get()
            ->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'current_price' => (float) $product->price,
                'total_quantity_sold' => (int) $product->total_quantity,
                'total_revenue' => round((float) $product->total_revenue, 2),
                'orders_count' => (int) $product->orders_count,
            ]);

        return [
            'sorted_by' => $by,
            'limit' => $limit,
            'products' => $topProducts->toArray(),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get orders for a specific period.
     */
    private function getOrdersForPeriod(string $period): array
    {
        $query = Order::query();

        match ($period) {
            'today' => $query->whereDate('created_at', today()),
            'this_week' => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'this_month' => $query->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month),
            'last_month' => $query->whereYear('created_at', now()->subMonth()->year)
                ->whereMonth('created_at', now()->subMonth()->month),
            default => $query,
        };

        $result = $query->selectRaw('count(*) as orders, sum(total) as revenue')->first();

        return [
            'orders' => $result->orders ?? 0,
            'revenue' => round((float) ($result->revenue ?? 0), 2),
        ];
    }

    /**
     * Get payment statistics.
     */
    private function getPaymentStatistics(): array
    {
        $completed = Payment::where('status', 'completed')->count();
        $pending = Payment::where('status', 'pending')->count();
        $failed = Payment::where('status', 'failed')->count();
        $totalAmount = Payment::where('status', 'completed')->sum('amount');

        $byMethod = Payment::where('status', 'completed')
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->payment_method => [
                    'count' => $item->count,
                    'total' => (float) $item->total,
                ]
            ]);

        return [
            'completed' => $completed,
            'pending' => $pending,
            'failed' => $failed,
            'total_amount' => round((float) $totalAmount, 2),
            'by_method' => $byMethod,
        ];
    }
}
