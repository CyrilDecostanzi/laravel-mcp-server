<?php

namespace App\Services\Analytics;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class CustomerInsightsService
{
    /**
     * Get customer insights and analytics.
     */
    public function getCustomerInsights(int $limit = 20): array
    {
        $topCustomers = $this->getTopCustomers($limit);
        $segments = $this->getCustomerSegments();
        $overview = $this->getCustomerOverview();

        return [
            'top_customers' => $topCustomers,
            'customer_segments' => $segments,
            'overview' => $overview,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get top customers by total revenue.
     */
    private function getTopCustomers(int $limit): array
    {
        return User::select(
            'users.id',
            'users.name',
            'users.email',
            DB::raw('COUNT(orders.id) as total_orders'),
            DB::raw('SUM(orders.total) as total_spent'),
            DB::raw('AVG(orders.total) as avg_order_value'),
            DB::raw('MIN(orders.created_at) as first_order_date'),
            DB::raw('MAX(orders.created_at) as last_order_date')
        )
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get()
            ->map(function ($customer) {
                $daysSinceFirst = now()->diffInDays($customer->first_order_date);
                $daysSinceLast = now()->diffInDays($customer->last_order_date);

                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'metrics' => [
                        'total_orders' => (int) $customer->total_orders,
                        'total_spent' => round((float) $customer->total_spent, 2),
                        'average_order_value' => round((float) $customer->avg_order_value, 2),
                        'first_order' => $customer->first_order_date,
                        'last_order' => $customer->last_order_date,
                        'days_since_last_order' => $daysSinceLast,
                        'customer_lifetime_days' => $daysSinceFirst,
                    ],
                    'segment' => $this->getCustomerSegment(
                        (int) $customer->total_orders,
                        (float) $customer->total_spent,
                        $daysSinceLast
                    ),
                ];
            })
            ->toArray();
    }

    /**
     * Get customer segments distribution.
     */
    private function getCustomerSegments(): array
    {
        return [
            'vip' => User::join('orders', 'users.id', '=', 'orders.user_id')
                ->select('users.id')
                ->groupBy('users.id')
                ->havingRaw('SUM(orders.total) >= 5000')
                ->count(),
            'loyal' => User::join('orders', 'users.id', '=', 'orders.user_id')
                ->select('users.id')
                ->groupBy('users.id')
                ->havingRaw('COUNT(orders.id) >= 5')
                ->havingRaw('SUM(orders.total) < 5000')
                ->count(),
            'at_risk' => User::join('orders', 'users.id', '=', 'orders.user_id')
                ->select('users.id')
                ->groupBy('users.id')
                ->havingRaw('MAX(orders.created_at) < ?', [now()->subDays(90)])
                ->count(),
            'one_time' => User::join('orders', 'users.id', '=', 'orders.user_id')
                ->select('users.id')
                ->groupBy('users.id')
                ->havingRaw('COUNT(orders.id) = 1')
                ->count(),
        ];
    }

    /**
     * Get overall customer statistics.
     */
    private function getCustomerOverview(): array
    {
        $totalCustomersWithOrders = User::has('orders')->count();
        $totalCustomers = User::count();

        // Calculate average lifetime value
        $averageLifetimeValue = DB::table('orders')
            ->select(DB::raw('AVG(user_totals.total_spent) as avg_ltv'))
            ->fromSub(function ($query) {
                $query->from('orders')
                    ->select('user_id', DB::raw('SUM(total) as total_spent'))
                    ->groupBy('user_id');
            }, 'user_totals')
            ->value('avg_ltv');

        return [
            'total_customers' => $totalCustomers,
            'customers_with_orders' => $totalCustomersWithOrders,
            'conversion_rate' => $totalCustomers > 0
                ? round(($totalCustomersWithOrders / $totalCustomers) * 100, 2).'%'
                : '0%',
            'average_lifetime_value' => round((float) $averageLifetimeValue, 2),
        ];
    }

    /**
     * Determine customer segment based on behavior.
     */
    public function getCustomerSegment(int $orders, float $totalSpent, int $daysSinceLast): string
    {
        if ($totalSpent >= 5000) {
            return 'VIP';
        }

        if ($daysSinceLast > 90) {
            return 'At Risk';
        }

        if ($orders >= 5) {
            return 'Loyal';
        }

        if ($orders === 1) {
            return 'One-Time';
        }

        return 'Regular';
    }
}
