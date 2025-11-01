<?php

namespace App\Mcp\Tools;

use App\Models\User;
use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class GetCustomerInsightsTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Get customer insights and analytics. Returns top customers by revenue, customer lifetime value, purchase frequency, and customer segments.
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
                    'description' => 'Number of top customers to return (default: 20)',
                    'default' => 20,
                    'minimum' => 1,
                    'maximum' => 100,
                ],
            ],
        ]);
    }

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $limit = min($request->params['limit'] ?? 20, 100);
        
        // Top customers by total revenue
        $topCustomers = User::select(
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
            });

        // Customer segments
        $segments = [
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

        // Overall customer statistics
        $totalCustomersWithOrders = User::has('orders')->count();
        $totalCustomers = User::count();
        
        // Calculate average lifetime value from orders grouped by user
        $averageLifetimeValue = DB::table('orders')
            ->select(DB::raw('AVG(user_totals.total_spent) as avg_ltv'))
            ->fromSub(function ($query) {
                $query->from('orders')
                    ->select('user_id', DB::raw('SUM(total) as total_spent'))
                    ->groupBy('user_id');
            }, 'user_totals')
            ->value('avg_ltv');

        $data = [
            'top_customers' => $topCustomers,
            'customer_segments' => $segments,
            'overview' => [
                'total_customers' => $totalCustomers,
                'customers_with_orders' => $totalCustomersWithOrders,
                'conversion_rate' => $totalCustomers > 0 
                    ? round(($totalCustomersWithOrders / $totalCustomers) * 100, 2) . '%'
                    : '0%',
                'average_lifetime_value' => round((float) $averageLifetimeValue, 2),
            ],
            'timestamp' => now()->toISOString(),
        ];

        return Response::text(json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Determine customer segment based on behavior.
     */
    private function getCustomerSegment(int $orders, float $totalSpent, int $daysSinceLast): string
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
